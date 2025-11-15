<?php
require_once 'config/function.php';
include 'config/db_connect.php';
$pageTitle = 'My Surveys';

// Previously this page required authentication; allow anonymous users to view available surveys

// Fetch available surveys from the database
$surveys = [];
$currentUserId = null;
if (!empty($_SESSION['user']['id'])) {
    $currentUserId = intval($_SESSION['user']['id']);
}

// Fetch surveys that the user hasn't completed yet
if ($currentUserId) {
    $surveyQuery = "SELECT s.id, s.name, s.description FROM surveys s LEFT JOIN survey_completions sc ON s.id = sc.survey_id AND sc.user_id = $currentUserId WHERE sc.id IS NULL";
} else {
    $surveyQuery = "SELECT id, name, description FROM surveys";
}

$result = mysqli_query($conn, $surveyQuery);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $surveys[] = $row;
    }
}
?>
<?php include 'includes/navbar.php'; ?>

<style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .content {
        flex: 1;
    }

    .survey-container {
        background-color: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    footer {
        margin-top: auto;
    }
</style>

<div class="container mb-5 content">
    <div class="py-3 bg-custom mb-3" style="border-radius: 10px;">
<div class="container">
    <div class="text-center py-3 px-4 mb-4" 
         style="background-color: #f8f4fb; border: 2px solid #7c2aa6; border-radius: 12px; display: inline-block;">
        <h2 class="fw-semi-bold m-0" style="color: #7c2aa6;">Available Surveys</h2>
    </div>
</div>


    </div>

    <div class="survey-container mb-5" style="background-color: #7c2aa6;">
        <div class="row">
            <?php
            // Resolve current user id from session (support different session shapes)
            $currentUserId = null;
            if (!empty($_SESSION['user']['id'])) {
                $currentUserId = intval($_SESSION['user']['id']);
            } elseif (!empty($_SESSION['user_id'])) {
                $currentUserId = intval($_SESSION['user_id']);
            } elseif (!empty($_SESSION['id'])) {
                $currentUserId = intval($_SESSION['id']);
            } else {
                // fallback: lookup by email if provided
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
            ?>
            <?php
            if (count($surveys) > 0):
                foreach ($surveys as $row):
                    $survey_id = $row['id'];
                    $questionQuery = "SELECT COUNT(*) as question_count FROM questions WHERE survey_id = $survey_id";
                    $questionResult = mysqli_query($conn, $questionQuery);
                    $questionCount = 0;
                    if ($questionResult) {
                        $qr = mysqli_fetch_assoc($questionResult);
                        $questionCount = $qr['question_count'] ?? 0;
                    }
            ?>
                    <div class="col-12 col-sm-6 col-md-4 mb-4">
                        <div class="card shadow-sm h-100 border-0" style="border-radius: 12px;">
                            <div class="card-body d-flex flex-column text-center">
                                <h5 class="card-title mb-2" style="color: #7c2aa6;">
                                    <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </h5>
                                <p class="mb-3 text-muted"><?= $questionCount ?> question(s) in this survey</p>
                                <div class="mt-auto">
                                    <?php
                                    $isCompleted = false;
                                    if ($currentUserId) {
                                        $sid = intval($row['id']);
                                        $check = mysqli_query($conn, "SELECT id FROM survey_completions WHERE user_id = $currentUserId AND survey_id = $sid LIMIT 1");
                                        if ($check) {
                                            $rnum = mysqli_num_rows($check);
                                            if ($rnum > 0) {
                                                $isCompleted = true;
                                            }
                                        }
                                    }

                                    if ($isCompleted): ?>
                                        <button class="btn btn-secondary px-4" style="border-radius:8px;" disabled>Completed</button>
                                    <?php else: ?>
                                        <a href="survey.php?id=<?= $row['id']; ?>" class="btn text-white px-4" style="background-color: #7c2aa6; border-radius: 8px;">Answer Now</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                endforeach;
            else:
            ?>
                <div class="col-12">
                    <h5 class="text-center text-muted">There are no available surveys at the moment.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
