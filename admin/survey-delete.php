<?php
session_start(); // Start session for flash messages
include('../config/db_connect.php'); // Include the database connection

// Validate and fetch survey ID
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']); // Ensure the ID is an integer
} else {
    die("Invalid survey ID."); // Exit if the survey ID is missing or invalid
}

// Start transaction for atomic operations
mysqli_begin_transaction($conn);

try {
    // Delete related responses by survey ID
    $delete_responses_by_survey = "DELETE FROM responses WHERE survey_id = $delete_id";
    if (!mysqli_query($conn, $delete_responses_by_survey)) {
        throw new Exception("Error deleting survey responses: " . mysqli_error($conn));
    }

    // Fetch sections associated with the survey
    $delete_sections = "SELECT id FROM sections WHERE survey_id = $delete_id";
    $sections_result = mysqli_query($conn, $delete_sections);
    if (!$sections_result) {
        throw new Exception("Error fetching sections: " . mysqli_error($conn));
    }

    while ($section = mysqli_fetch_assoc($sections_result)) {
        $section_id = $section['id'];

        // Fetch questions associated with each section
        $delete_questions = "SELECT id FROM questions WHERE section_id = $section_id";
        $questions_result = mysqli_query($conn, $delete_questions);
        if (!$questions_result) {
            throw new Exception("Error fetching questions: " . mysqli_error($conn));
        }

        while ($question = mysqli_fetch_assoc($questions_result)) {
            $question_id = $question['id'];

            // Delete responses associated with the question
            $delete_responses_by_question = "DELETE FROM responses WHERE question_id = $question_id";
            if (!mysqli_query($conn, $delete_responses_by_question)) {
                throw new Exception("Error deleting question responses: " . mysqli_error($conn));
            }

            // Delete options for each question
            $delete_options = "DELETE FROM options WHERE question_id = $question_id";
            if (!mysqli_query($conn, $delete_options)) {
                throw new Exception("Error deleting options: " . mysqli_error($conn));
            }

            // Delete the question itself
            $delete_question = "DELETE FROM questions WHERE id = $question_id";
            if (!mysqli_query($conn, $delete_question)) {
                throw new Exception("Error deleting question: " . mysqli_error($conn));
            }
        }

        // Delete the section
        $delete_section = "DELETE FROM sections WHERE id = $section_id";
        if (!mysqli_query($conn, $delete_section)) {
            throw new Exception("Error deleting section: " . mysqli_error($conn));
        }
    }

    // Finally, delete the survey
    $delete_survey = "DELETE FROM surveys WHERE id = $delete_id";
    if (!mysqli_query($conn, $delete_survey)) {
        throw new Exception("Error deleting survey: " . mysqli_error($conn));
    }

    // Commit the transaction
    mysqli_commit($conn);

    $_SESSION['message'] = "Survey and its related data deleted successfully!";
    header('Location: survey.php');
    exit();
} catch (Exception $e) {
    // Rollback the transaction on error
    mysqli_rollback($conn);
    die($e->getMessage());
}
?>
