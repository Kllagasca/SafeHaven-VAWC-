<?php
include '../config/db_connect.php';

// Get the survey ID from the URL
$survey_id = $_GET['id'];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid survey ID");
}

// Fetch the survey name for the specific survey_id
$surveyQuery = "SELECT name, description FROM surveys WHERE id = $survey_id";
$surveyResult = $conn->query($surveyQuery);
$survey = $surveyResult->fetch_assoc(); // Get survey data

$totalQuery = "
    SELECT COUNT(*) as total 
    FROM responses r
    LEFT JOIN survey_verifications sv ON r.verification_id = sv.id
    JOIN questions q ON r.question_id = q.id
    JOIN sections s ON q.section_id = s.id
    WHERE s.survey_id = $id
      AND (r.verification_id IS NULL OR sv.status = 'accepted')
";
$totalResult = mysqli_query($conn, $totalQuery);
$totalResponses = mysqli_fetch_assoc($totalResult)['total'];

$totalQuestionsQuery = "
    SELECT COUNT(*) as total_questions
    FROM questions
    WHERE section_id IN (
        SELECT id FROM sections WHERE survey_id = $id
    )
";
$totalQuestionsResult = mysqli_query($conn, $totalQuestionsQuery);
$totalQuestions = mysqli_fetch_assoc($totalQuestionsResult)['total_questions'];

$trendQuery = "
        SELECT DATE(r.created_at) as date, COUNT(*) as count 
        FROM responses r
        LEFT JOIN survey_verifications sv ON r.verification_id = sv.id
        JOIN questions q ON r.question_id = q.id
        JOIN sections s ON q.section_id = s.id
        WHERE s.survey_id = $id
            AND (r.verification_id IS NULL OR sv.status = 'accepted')
        GROUP BY DATE(r.created_at)
        ORDER BY date ASC
";
$trendResult = mysqli_query($conn, $trendQuery);
$trendData = mysqli_fetch_all($trendResult, MYSQLI_ASSOC);


// Fetch questions for the specific survey
$questionsQuery = "SELECT id, question FROM questions WHERE survey_id = $survey_id";
$questionsResult = $conn->query($questionsQuery);
$questions = [];
if ($questionsResult->num_rows > 0) {
    while ($row = $questionsResult->fetch_object()) {
        $questions[] = $row;
    }
}

// === Simple AI analysis (rule-based) ===
// Produce a short decision summary using trend data and per-question top-answer stats.
$ai_decision_text = '';
$ai_reasons = [];
// analyze trend: compare recent 7-day sum vs previous 7-day sum if possible
$trendCounts = array_map(function($r){ return (int)$r['count']; }, $trendData);
$trendDates = array_map(function($r){ return $r['date']; }, $trendData);
$trendCountTotal = array_sum($trendCounts);
if (count($trendCounts) >= 14) {
    $recent = array_slice($trendCounts, -7);
    $prev = array_slice($trendCounts, -14, 7);
    $recentSum = array_sum($recent);
    $prevSum = array_sum($prev);
    if ($prevSum == 0) {
        $trendPct = ($recentSum > 0) ? 100 : 0;
    } else {
        $trendPct = round((($recentSum - $prevSum) / max(1, $prevSum)) * 100, 1);
    }
    if ($trendPct >= 20) {
        $ai_reasons[] = "Responses increased by {$trendPct}% in the most recent 7-day window compared to the previous 7 days.";
    } elseif ($trendPct <= -20) {
        $ai_reasons[] = "Responses decreased by {$trendPct}% in the most recent 7-day window compared to the previous 7 days.";
    } else {
        $ai_reasons[] = "Response volume stable over the recent period ({$trendPct}% change).";
    }
} elseif (count($trendCounts) >= 2) {
    // compare last two points
    $last = end($trendCounts);
    $prev = prev($trendCounts);
    if ($prev == 0) $trendPct = ($last > 0) ? 100 : 0; else $trendPct = round((($last - $prev) / max(1, $prev)) * 100, 1);
    $ai_reasons[] = "Recent change between last two days: {$trendPct}%";
} else {
    $ai_reasons[] = "Insufficient trend data to detect changes.";
}

// analyze per-question top answers to detect strong consensus
$strongConsensus = [];
foreach ($questions as $q) {
    $qid = intval($q->id);
    $sql = "SELECT answer, COUNT(*) as cnt FROM responses r LEFT JOIN survey_verifications sv ON r.verification_id = sv.id WHERE r.question_id = $qid AND (r.verification_id IS NULL OR sv.status = 'accepted') GROUP BY answer";
    $res = mysqli_query($conn, $sql);
    $counts = [];
    $totalQ = 0;
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $ans = (string)$row['answer'];
            $c = (int)$row['cnt'];
            $counts[$ans] = $c;
            $totalQ += $c;
        }
    }
    if ($totalQ > 0) {
        arsort($counts);
        $topAnswer = array_key_first($counts);
        $topCount = $counts[$topAnswer];
        $topPct = round(($topCount / $totalQ) * 100, 1);
        if ($topPct >= 70) {
            $strongConsensus[] = [
                'question' => $q->question,
                'top' => $topAnswer,
                'pct' => $topPct
            ];
        }
    }
}

// Build a decision from signals
if (!empty($strongConsensus)) {
    $ai_decision_text = "High priority: one or more questions show a strong consensus. Review these questions for action.";
    foreach ($strongConsensus as $sc) {
        $ai_reasons[] = "Question: {$sc['question']} — top answer '{$sc['top']}' at {$sc['pct']}%";
    }
    // bump priority if trend is also rising
    if (isset($trendPct) && $trendPct >= 20) {
        $ai_decision_text = "Urgent: Strong consensus combined with rising response volume — immediate review recommended.";
    }
} else {
    if (isset($trendPct) && $trendPct >= 20) {
        $ai_decision_text = "Medium priority: Response volume is increasing notably; consider follow-up or investigation.";
    } else {
        $ai_decision_text = "No immediate action suggested: data appears stable or insufficient for a decisive recommendation.";
    }
}

// Build a short explanatory paragraph list: answers, trends, remarks, conclusions, recommendations
$ai_explanation_lines = [];
// Answers summary
if (!empty($strongConsensus)) {
    $parts = [];
    foreach ($strongConsensus as $sc) {
        $parts[] = "{$sc['question']} — '{$sc['top']}' ({$sc['pct']}%)";
    }
    $ai_explanation_lines[] = "Answers: Strong consensus detected on the following items: " . implode('; ', $parts) . ".";
} else {
    $ai_explanation_lines[] = "Answers: No strong consensus detected across questions; responses are more distributed or inconclusive.";
}

// Trend summary
if (isset($trendPct)) {
    $ai_explanation_lines[] = "Trend: recent change estimated at {$trendPct}%. " . (isset($recentSum) ? "Recent period count: {$recentSum}." : "");
} else {
    $ai_explanation_lines[] = "Trend: insufficient historical data to compute a robust trend.";
}

// Remarks (counts)
$ai_explanation_lines[] = "Remarks: {$totalResponses} total accepted responses across {$totalQuestions} questions. " . (($totalResponses < 10) ? "Note: sample size is small; interpret results cautiously." : "");

// Recommendations
if (strpos($ai_decision_text, 'Urgent') !== false) {
    $ai_explanation_lines[] = "Recommendations: Immediate review recommended. Prioritize follow-up on the identified questions, notify stakeholders, and consider targeted interventions.";
} elseif (strpos($ai_decision_text, 'High') !== false) {
    $ai_explanation_lines[] = "Recommendations: Review the questions with strong consensus and plan actions or further investigation as appropriate.";
} elseif (strpos($ai_decision_text, 'Medium') !== false) {
    $ai_explanation_lines[] = "Recommendations: Monitor the trend closely and consider targeted follow-up surveys or outreach if the increase continues.";
} else {
    $ai_explanation_lines[] = "Recommendations: No immediate action required; continue monitoring and collect more data if possible.";
}

// Conclusion / Decision (repeat for clarity)
$ai_explanation_lines[] = "Decision: {$ai_decision_text}";

?>

<head>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

    <div class="text-center mb-4">
            <img src="../assets/img/logo.png" alt="Logo" class="img-fluid" style="max-width: 200px;">
        </div>

        <h1 class="text-center text-white"><?php echo htmlspecialchars($survey['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="text-center text-white mb-5"><?php echo htmlspecialchars($survey['description'], ENT_QUOTES, 'UTF-8'); ?></p>
<body class="bg-light">
    <div class="container mt-5">

    <div class="text-end mb-3">
        <a href="survey.php" class="btn text-decoration-none bg-primary text-white font-weight-bold">
            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back to Surveys
        </a>
    </div>

    <h4 class="text-white">Survey Responses</h4>

    <?php if (!empty($ai_decision_text)): ?>
        <div class="card mt-3 mb-4" style="background-color: rgba(255,255,255,0.95);">
            <div class="card-body text-dark">
                <h5 class="card-title">BI Decision</h5>
                <p class="card-text mb-2"><?php echo htmlspecialchars($ai_decision_text, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if (!empty($ai_explanation_lines)): ?>
                    <div class="mb-2">
                        <?php foreach ($ai_explanation_lines as $line): ?>
                            <p class="mb-1"><?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($ai_reasons)): ?>
                    <p class="mb-1"><strong>Reasons:</strong></p>
                    <ul>
                        <?php foreach ($ai_reasons as $r): ?>
                            <li><?php echo htmlspecialchars($r, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

        <!-- Logo Section -->
        <?php foreach ($questions as $question): ?>
            <?php
            // Fetch response counts for each answer to this question
            $responsesQuery = "
                SELECT answer, COUNT(*) as count 
                FROM responses 
                WHERE question_id = {$question->id} 
                GROUP BY answer
            ";
            $responsesResult = $conn->query($responsesQuery);
            $responseData = [];
            if ($responsesResult->num_rows > 0) {
                while ($row = $responsesResult->fetch_object()) {
                    $responseData[] = "{ name: '" . addslashes($row->answer) . "', y: " . $row->count . " }";
                }
            }
            ?>


            <!-- Back Button -->

            <!-- Create Chart for Each Question -->
            <figure class="highcharts-figure survey-container p-5 text-white rounded shadow-sm mb-3" style="background-color:rgba(0, 0, 0, 0.51);">

    <?php
    // === safe rendering per question ===
    $qid = intval($question->id);

    // detect type
    $type = 'choice';
    if (!empty($question->type)) {
        $type = $question->type;
    } else {
        $qtext = strtolower($question->question);
        if (strpos($qtext, 'email') !== false || strpos($qtext, 'gmail') !== false || strpos($qtext, 'name') !== false) {
            $type = 'text';
        }
    }

    // fetch responses (only include accepted verifications or no verification)
    $responses = [];
    $sql = "SELECT r.answer, r.created_at FROM responses r LEFT JOIN survey_verifications sv ON r.verification_id = sv.id WHERE r.question_id = $qid AND (r.verification_id IS NULL OR sv.status = 'accepted')";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $responses[] = $row;
        }
    }

    if (empty($responses)) {
        echo '<p class="text-center"><em>No responses yet.</em></p>';
    } else {
        if ($type === 'choice') {
            $counts = [];
            foreach ($responses as $r) {
                $ans = (string)$r['answer'];
                if ($ans === '') $ans = '(No answer)';
                $counts[$ans] = ($counts[$ans] ?? 0) + 1;
            }
            $responseDataParts = [];
            foreach ($counts as $ans => $count) {
                $responseDataParts[] = json_encode(['name' => $ans, 'y' => (int)$count]);
            }
            ?>
            <div id="container-<?php echo $qid; ?>" style="min-width:300px; height:400px; margin: 0 auto;"></div>
            <script>
            Highcharts.chart('container-<?php echo $qid; ?>', {
                chart: { type: 'pie' },
                title: { text: <?php echo json_encode("Question: " . $question->question); ?> },
                subtitle: { text: 'Source: YourSurvey' },
                tooltip: { pointFormat: '<b>{point.percentage:.1f}%</b> ({point.y})' },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: { enabled: true, format: '{point.name}: {point.percentage:.1f} %' }
                    }
                },
                series: [{ name: 'Answers', colorByPoint: true, data: [<?php echo implode(',', $responseDataParts); ?>] }]
            });
            </script>
            <?php
            // === Analysis / Feedback card for choice questions ===
            $totalForQuestion = array_sum($counts);
            // find top answer
            arsort($counts);
            $topAnswer = array_key_first($counts);
            $topCount = $counts[$topAnswer];
            $topPct = $totalForQuestion > 0 ? round(($topCount / $totalForQuestion) * 100, 1) : 0;
            // simple implication heuristic
            $safeTop = htmlspecialchars(stripslashes((string)$topAnswer), ENT_QUOTES, 'UTF-8');
            $implication = '';
            if ($topPct >= 70) {
                $implication = "There is a strong consensus for '" . $safeTop . "'; this likely reflects a clear preference among respondents.";
            } elseif ($topPct >= 50) {
                $implication = "A majority selected '" . $safeTop . "'; this suggests the option is generally preferred but not unanimous.";
            } elseif ($topPct >= 30) {
                $implication = "The top answer '" . $safeTop . "' has a plurality, indicating mixed opinions; consider follow-up for clarity.";
            } else {
                $implication = "No clear consensus: the top answer '" . $safeTop . "' is only slightly more common than others; you may want more data or a follow-up question.";
            }
            ?>
            <div class="card mt-3 mb-4" style="background-color: rgba(255,255,255,0.95);">
                <div class="card-body text-dark">
                    <h5 class="card-title">Analysis</h5>
                    <p class="card-text mb-1"><strong>Total responses:</strong> <?php echo (int)$totalForQuestion; ?></p>
                    <p class="card-text mb-1"><strong>Top answer:</strong> <?php echo $safeTop; ?> (<?php echo $topPct; ?>%)</p>
                    <p class="card-text mb-2"><strong>What this implies:</strong> <?php echo $implication; ?></p>
                    <p class="card-text small text-muted">This is an automated summary. Use it as a quick insight; review raw responses for context.</p>
                </div>
            </div>
            <?php
        } else {
            echo '<div style=" padding:15px; border-radius:8px; margin-bottom:20px;">';
            echo '<h4 style="color:#fff;">' . htmlspecialchars($question->question) . '</h4>';
            echo '<ul style="list-style-type: disc; padding-left:20px; color:#fff;">';
            foreach ($responses as $r) {
                echo '<li>' . htmlspecialchars($r['answer']) . '</li>';
            }
            echo '</ul>';
            echo '</div>';

                // === Analysis / Feedback card for text questions ===
                $totalForQuestion = count($responses);
                // determine most common text (simple frequency)
                $textValues = array_map('trim', array_map('strval', array_column($responses, 'answer')));
                $textCounts = array_count_values($textValues);
                arsort($textCounts);
                $topText = key($textCounts);
                $topTextCount = $textCounts[$topText] ?? 0;
                $topTextPct = $totalForQuestion > 0 ? round(($topTextCount / $totalForQuestion) * 100, 1) : 0;
                // implication for text responses
                $safeTopText = htmlspecialchars(stripslashes((string)$topText), ENT_QUOTES, 'UTF-8');
                if ($topTextPct >= 50) {
                    $textImplication = "Many respondents mentioned '" . $safeTopText . "' which suggests this is a common concern or theme.";
                } elseif ($topTextPct >= 25) {
                    $textImplication = "Several respondents mentioned '" . $safeTopText . "'; it may be a notable theme worth investigating further.";
                } else {
                    $textImplication = "Responses are varied; no single theme dominates. Consider qualitative review for patterns.";
                }
                // show up to 5 sample responses
                $samples = array_slice($textValues, 0, 5);
                ?>
                <div class="card mt-3 mb-4" style="background-color: rgba(255,255,255,0.95);">
                    <div class="card-body text-dark">
                        <h5 class="card-title">Analysis</h5>
                        <p class="card-text mb-2"><strong>Total responses:</strong> <?php echo (int)$totalForQuestion; ?></p>
                        <p class="card-text mb-1"><strong>Sample responses:</strong></p>
                        <ul class="mb-0">
                            <?php foreach ($samples as $s): ?>
                                <li><?php echo htmlspecialchars(stripslashes($s)); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ($totalForQuestion > count($samples)): ?>
                            <p class="card-text small text-muted mt-2">And <?php echo $totalForQuestion - count($samples); ?> more responses not shown.</p>
                        <?php endif; ?>
                        <p class="card-text mb-2"><strong>What this implies:</strong> <?php echo $textImplication; ?></p>
                    </div>
                </div>
                <?php
                } // end else (text answers block)
            } // end if (empty/responses) else
            ?>
</figure>




        <?php endforeach; ?>
    </div>

<?php
// (pending verifications rendered above)
?>

    <style>

::-webkit-scrollbar {
            display: none;
        }
        html {
            scrollbar-width: none;
        }
        .bg-light {
            background-image: url('../assets/img/survey-bg.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .survey-container {
            font-family: Montserrat, sans-serif;
        }
        .highcharts-description {
            color: #fff;
            font-size: 1.1em;
        }
    </style>
</body>