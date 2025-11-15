<?php
include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Invalid method');
 $vid = isset($_POST['verification_id']) ? intval($_POST['verification_id']) : 0;
 $action = isset($_POST['action']) ? $_POST['action'] : '';
if (!$vid || !in_array($action, ['accept','reject'])) die('Invalid request');

$status = $action === 'accept' ? 'accepted' : 'rejected';
$stmt = $conn->prepare("UPDATE survey_verifications SET status = ? WHERE id = ?");
$stmt->bind_param('si', $status, $vid);
$stmt->execute();
$stmt->close();

// If rejected, optionally delete responses attached to this verification so they don't show in charts
if ($status === 'rejected') {
    $d = $conn->prepare("DELETE FROM responses WHERE verification_id = ?");
    $d->bind_param('i', $vid);
    $d->execute();
    $d->close();
}

// Redirect back to the admin responses page for the survey
$surveyId = isset($_POST['survey_id']) ? intval($_POST['survey_id']) : 0;
if ($surveyId) {
    header('Location: responses.php?id=' . $surveyId);
} else {
    header('Location: responses.php');
}
exit();
?>