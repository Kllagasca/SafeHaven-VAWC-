<?php
include('config/function.php');
include('config/db_connect.php'); // Include the database connection

// Get the survey ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid survey ID");
}

// Previously this page required authentication. That guard was removed so
// anonymous users can view and submit surveys. We still attempt to resolve a
// current user id later when recording completions, but non-logged-in users
// may submit anonymously.

$surveyQuery = "SELECT name, description FROM surveys WHERE id = $id";
$surveyResult = mysqli_query($conn, $surveyQuery);
if ($surveyResult && mysqli_num_rows($surveyResult) > 0) {
    $survey = mysqli_fetch_assoc($surveyResult); // Fetch all survey metadata
} else {
    die("Survey not found.");
}

// Fetch survey sections
$sectionsQuery = "SELECT * FROM sections WHERE survey_id = $id";
$sectionsResult = mysqli_query($conn, $sectionsQuery);
$sections = mysqli_fetch_all($sectionsResult, MYSQLI_ASSOC);

// Fetch survey questions for each section
$questions = [];
foreach ($sections as $section) {
    $questionsQuery = "SELECT * FROM questions WHERE section_id = {$section['id']}";
    $questionsResult = mysqli_query($conn, $questionsQuery);
    if ($questionsResult) {
        $questions[$section['id']] = mysqli_fetch_all($questionsResult, MYSQLI_ASSOC);
    }
}

// Fetch options for each question
$options = [];
foreach ($questions as $sectionId => $sectionQuestions) {
    foreach ($sectionQuestions as $question) {
        if ($question['type'] == 'radio' || $question['type'] == 'checkbox') {
            $optionsQuery = "SELECT * FROM options WHERE question_id = {$question['id']}";
            $optionsResult = mysqli_query($conn, $optionsQuery);
            if ($optionsResult) {
                $options[$question['id']] = mysqli_fetch_all($optionsResult, MYSQLI_ASSOC);
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['answer'])) {
        foreach ($_POST['answer'] as $questionId => $response) {
            // Handle multiple checkbox responses
            if (is_array($response)) {
                foreach ($response as $value) {
                    $escapedValue = mysqli_real_escape_string($conn, $value);
                        // attach verification id if present in session
                        $verificationId = null;
                        if (!empty($_SESSION['survey_verification'][$id])) {
                            $verificationId = intval($_SESSION['survey_verification'][$id]);
                        }

                        if ($verificationId) {
                            $insertQuery = "INSERT INTO responses (question_id, answer, verification_id) VALUES ($questionId, '$escapedValue', $verificationId)";
                        } else {
                            $insertQuery = "INSERT INTO responses (question_id, answer) VALUES ($questionId, '$escapedValue')";
                        }
                    mysqli_query($conn, $insertQuery);
                }
            } else {
                // Handle single text or radio responses
                $escapedValue = mysqli_real_escape_string($conn, $response);
                    if ($verificationId) {
                        $insertQuery = "INSERT INTO responses (question_id, answer, verification_id) VALUES ($questionId, '$escapedValue', $verificationId)";
                    } else {
                        $insertQuery = "INSERT INTO responses (question_id, answer) VALUES ($questionId, '$escapedValue')";
                    }
                mysqli_query($conn, $insertQuery);
            }
        }
        // Mark survey as completed for the logged-in user (if known)
        $currentUserId = null;
        if (!empty($_SESSION['user']['id'])) {
            $currentUserId = intval($_SESSION['user']['id']);
        } elseif (!empty($_SESSION['user_id'])) {
            $currentUserId = intval($_SESSION['user_id']);
        } elseif (!empty($_SESSION['id'])) {
            $currentUserId = intval($_SESSION['id']);
        } else {
            $safeEmail = null;
            if (!empty($_SESSION['email'])) {
                $safeEmail = mysqli_real_escape_string($conn, $_SESSION['email']);
            } elseif (!empty($_SESSION['user']['email'])) {
                $safeEmail = mysqli_real_escape_string($conn, $_SESSION['user']['email']);
            }
            if ($safeEmail) {
                $uidRes = mysqli_query($conn, "SELECT id FROM users WHERE email = '$safeEmail' LIMIT 1");
                if ($uidRes && mysqli_num_rows($uidRes) > 0) {
                    $uidRow = mysqli_fetch_assoc($uidRes);
                    $currentUserId = intval($uidRow['id']);
                }
            }
        }

        // Ensure completions table exists
        $createTableSql = "CREATE TABLE IF NOT EXISTS survey_completions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            survey_id INT NOT NULL,
            completed_at DATETIME NOT NULL,
            UNIQUE KEY uniq_user_survey (user_id, survey_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        mysqli_query($conn, $createTableSql);

        if ($currentUserId) {
            $surveyId = intval($id);
            // Insert completion if not already present (use INSERT IGNORE to avoid duplicates)
            $ins = mysqli_query($conn, "INSERT IGNORE INTO survey_completions (user_id, survey_id, completed_at) VALUES ($currentUserId, $surveyId, NOW())");
        }

        header("Location: complete_survey.php");
        exit();
    } else {
        echo "<script>alert('Please fill out all questions before submitting.');</script>";
    }
}

// Prevent re-taking the survey: check if current user already completed
$alreadyCompleted = false;
if (!empty($_SESSION['user']['id']) || !empty($_SESSION['user_id']) || !empty($_SESSION['id']) || !empty($_SESSION['email']) || !empty($_SESSION['user']['email'])) {
    // resolve current user id first
    $currentUserId = null;
    if (!empty($_SESSION['user']['id'])) {
        $currentUserId = intval($_SESSION['user']['id']);
    } elseif (!empty($_SESSION['user_id'])) {
        $currentUserId = intval($_SESSION['user_id']);
    } elseif (!empty($_SESSION['id'])) {
        $currentUserId = intval($_SESSION['id']);
    } else {
        $safeEmail = !empty($_SESSION['email']) ? mysqli_real_escape_string($conn, $_SESSION['email']) : mysqli_real_escape_string($conn, $_SESSION['user']['email']);
        $uidRes = mysqli_query($conn, "SELECT id FROM users WHERE email = '$safeEmail' LIMIT 1");
        if ($uidRes && mysqli_num_rows($uidRes) > 0) {
            $uidRow = mysqli_fetch_assoc($uidRes);
            $currentUserId = intval($uidRow['id']);
        }
    }
    if ($currentUserId) {
        $chk = mysqli_query($conn, "SELECT id FROM survey_completions WHERE user_id = $currentUserId AND survey_id = $id LIMIT 1");
        if ($chk && mysqli_num_rows($chk) > 0) {
            $alreadyCompleted = true;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        ::-webkit-scrollbar {
            display: none;
        }
        html {
            scrollbar-width: none;
        }
        body {
            background-image: url('assets/img/survey-bg.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Montserrat, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .logo-container img {
            width: 200px;
            height: 200px;
        }
        .survey-container {
            background: rgba(0, 0, 0, 0.51);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #333;
        }
        button {
            background: #007BFF;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        p {
            font-weight: normal;
        }
    </style>
</head>
<body>
<?php include('includes/navbar.php'); ?>

    <!-- Main Container -->
    <div class="container my-5">
        <!-- Logo Section -->
        <div class="logo-container">
            <img src="assets/img/logo.png" alt="Logo">
        </div>

        <!-- Survey Title -->
        <div class="text-center mb-4 text-white">
            <h1><?php echo htmlspecialchars($survey['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        </div>

        <div class="mx-auto text-center text-white">
            <h5><?php echo htmlspecialchars($survey['description'], ENT_QUOTES, 'UTF-8'); ?></h5>
        </div>

        <!-- Survey Form -->
        <div class="survey-container mx-auto mt-4 text-white">
            <?php if ($alreadyCompleted): ?>
                <div class="text-center py-5">
                    <h4 class="mb-3">You have already completed this survey.</h4>
                    <a href="index.php" class="btn btn-secondary">Back to Home</a>
                </div>
            <?php else: ?>
            <form method="POST">
                <?php foreach ($sections as $section): ?>
                    <div class="mb-4">
                        <h3 style="color: #b093ff;"><?php echo htmlspecialchars($section['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($section['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>

                    <?php if (isset($questions[$section['id']])): ?>
                        <?php foreach ($questions[$section['id']] as $question): ?>
                            <!-- Card Wrapper for Each Question -->
                            <div class="card mb-3 text-dark">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong><?php echo htmlspecialchars($question['question'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </p>

                                    <?php if ($question['type'] == 'text'): ?>
                                        <input type="text" class="form-control font-weight:bold" name="answer[<?php echo $question['id']; ?>]" required>
                                    <?php elseif ($question['type'] == 'radio'): ?>
                                        <!-- Radio Buttons -->
                                        <?php if (isset($options[$question['id']])): ?>
                                            <?php foreach ($options[$question['id']] as $option): ?>
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($option['option_text'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                                    <label class="form-check-label"><?php echo htmlspecialchars($option['option_text'], ENT_QUOTES, 'UTF-8'); ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php elseif ($question['type'] == 'checkbox'): ?>
                                        <!-- Checkbox Options -->
                                        <?php if (isset($options[$question['id']])): ?>
                                            <?php foreach ($options[$question['id']] as $option): ?>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="answer[<?php echo $question['id']; ?>][]" value="<?php echo htmlspecialchars($option['option_text'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <label class="form-check-label"><?php echo htmlspecialchars($option['option_text'], ENT_QUOTES, 'UTF-8'); ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-danger">Unknown question type</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <!-- Button Section -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-sm">Submit Survey</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
