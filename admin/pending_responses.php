<?php
ob_start();
include('../config/db_connect.php');

// Get survey id
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die('Invalid survey id');
}

$surveyQ = "SELECT name, description FROM surveys WHERE id = $id";
$surveyR = $conn->query($surveyQ);
$survey = $surveyR->fetch_assoc();
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
    <p class="text-center text-white mb-4"><?php echo htmlspecialchars($survey['description'], ENT_QUOTES, 'UTF-8'); ?></p>

<body class="bg-light">
    <div class="container mt-4">

    <div class="text-end mb-3">
        <a href="survey.php" class="btn text-decoration-none bg-primary text-white font-weight-bold">
            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back to Surveys
        </a>
        <a href="responses.php?id=<?php echo $id; ?>" class="btn btn-secondary ms-2">View Graphs</a>
    </div>

    <h4 class="text-white">Pending Verifications</h4>

<?php
    $pendingQ = "SELECT * FROM survey_verifications WHERE survey_id = $id AND status = 'pending' ORDER BY created_at DESC";
    $pendingR = mysqli_query($conn, $pendingQ);
    if ($pendingR && mysqli_num_rows($pendingR) > 0):
        while ($pv = mysqli_fetch_assoc($pendingR)):
?>
            <figure class="highcharts-figure survey-container p-3 text-white rounded shadow-sm mb-2" style="background-color:rgba(0, 0, 0, 0.51);">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center mb-2">
                        <div class="card" style="width:1400px;">
                            <?php if (!empty($pv['image'])): ?>
                                <a href="../<?= htmlspecialchars($pv['image']) ?>" target="_blank" rel="noopener noreferrer">
                                    <img src="../<?= htmlspecialchars($pv['image']) ?>" class="card-img-top" style="height:300px; object-fit:cover;" />
                                </a>
                            <?php else: ?>
                                <div style="height:200px; background:#eee; display:flex; align-items:center; justify-content:center; color:#333;">No ID</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 mb-2">
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <p class="text-white mb-0"><strong>Name:</strong> <?= htmlspecialchars($pv['name']) ?></p>
                                    <p class="text-white mb-0"><strong>Gender:</strong> <?= htmlspecialchars($pv['gender']) ?></p>
                                    <p class="text-white mb-0"><strong>Address:</strong> <?= nl2br(htmlspecialchars($pv['address'])) ?></p>
                                    <p class="small text-muted mb-0">Submitted: <?= htmlspecialchars(format_datetime($pv['created_at'])) ?></p>
                                </div>
                                <div class="col-md-3 d-flex justify-content-end">
                                    <form method="post" action="verify_response_action.php">
                                        <input type="hidden" name="verification_id" value="<?= intval($pv['id']) ?>">
                                        <input type="hidden" name="survey_id" value="<?= intval($id) ?>">
                                        <input type="hidden" name="return_to" value="pending_responses.php?id=<?= intval($id) ?>">
                                        <div class="d-flex gap-1">
                                            <button name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                                            <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                </div>
            </figure>
<?php
        endwhile;
    else:
?>
        <div class="alert alert-info">No pending verifications.</div>
<?php
    endif;
?>

    </div>

    <style>
        ::-webkit-scrollbar {
            display: none;
        }
        html {
            scrollbar-width: none;
        }
        body {
            background-image: url('../assets/img/survey-bg.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .survey-container {
            font-family: Montserrat, sans-serif;
        }
    </style>

</body>

<?php ob_end_flush(); ?>
