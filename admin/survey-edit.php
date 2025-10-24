<?php
// Start output buffering
ob_start(); 

include('includes/header.php');
include('../config/db_connect.php'); // Include the database connection
// Initialize $sections as an empty array

$sections = [];

// Get the survey ID from the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure $id is an integer to prevent SQL injection

    // Fetch sections associated with the survey
    $sections_query = "SELECT * FROM sections WHERE survey_id = $id";
    $sections_result = mysqli_query($conn, $sections_query);
    if ($sections_result && mysqli_num_rows($sections_result) > 0) {
        $sections = mysqli_fetch_all($sections_result, MYSQLI_ASSOC);
    }

    // Fetch options for each question (if any)
    $options = [];
    $questions = []; // Initialize $questions to avoid undefined variable errors
    foreach ($sections as $section) {
        $questions_query = "SELECT * FROM questions WHERE section_id = {$section['id']}";
        $questions_result = mysqli_query($conn, $questions_query);
        if ($questions_result && mysqli_num_rows($questions_result) > 0) {
            $questions[$section['id']] = mysqli_fetch_all($questions_result, MYSQLI_ASSOC);
        } else {
            $questions[$section['id']] = [];
        }

        // Fetch options for each question
        foreach ($questions[$section['id']] as $question) {
            if ($question['type'] == 'radio' || $question['type'] == 'checkbox') {
                $options_query = "SELECT * FROM options WHERE question_id = {$question['id']}";
                $options_result = mysqli_query($conn, $options_query);
                $options[$question['id']] = mysqli_fetch_all($options_result, MYSQLI_ASSOC);
            }
        }
    }
}



// Handle the update for the questions, options, and sections
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle option removal
    if (isset($_POST['remove_option_id']) && $_POST['remove_option_id'] !== 'new') {
        $remove_option_id = intval($_POST['remove_option_id']);
        // Delete the option from the database
        $remove_option_query = "DELETE FROM options WHERE id = $remove_option_id";
        if (mysqli_query($conn, $remove_option_query)) {
            redirect("survey-edit.php?id=$id", 'Option removed successfully!');
        } else {
            echo "Error removing option: " . mysqli_error($conn);
        }
        exit();
    }

    // Handle section removal
    if (isset($_POST['remove_section_id'])) {
        $remove_section_id = intval($_POST['remove_section_id']);
        // Delete questions and their associated options for the section
        $questions_query = "SELECT id FROM questions WHERE section_id = $remove_section_id";
        $questions_result = mysqli_query($conn, $questions_query);
        while ($question = mysqli_fetch_assoc($questions_result)) {
            // Delete options for each question
            mysqli_query($conn, "DELETE FROM options WHERE question_id = {$question['id']}");
        }
        // Delete the questions
        mysqli_query($conn, "DELETE FROM questions WHERE section_id = $remove_section_id");
        // Delete the section
        mysqli_query($conn, "DELETE FROM sections WHERE id = $remove_section_id");
        redirect("survey-edit.php?id=$id", 'Section removed successfully!');
        exit();
    }

    // Handle question removal
    if (isset($_POST['remove_question_id'])) {
        $remove_question_id = intval($_POST['remove_question_id']);
        // Delete the question and its associated options
        mysqli_query($conn, "DELETE FROM options WHERE question_id = $remove_question_id");
        mysqli_query($conn, "DELETE FROM questions WHERE id = $remove_question_id");
        redirect("survey-edit.php?id=$id", 'Question removed successfully!');
        exit();
    }

    // Handle adding a new question
    if (isset($_POST['new_question_text']) && isset($_POST['new_question_type']) && isset($_POST['section_id'])) {
        $new_question_text = mysqli_real_escape_string($conn, $_POST['new_question_text']);
        $new_question_type = mysqli_real_escape_string($conn, $_POST['new_question_type']);
        $section_id = intval($_POST['section_id']);

        // Insert new question into the database
        $sql = "INSERT INTO questions (section_id, question, type) VALUES ('$section_id', '$new_question_text', '$new_question_type')";
        if (mysqli_query($conn, $sql)) {
            $question_id = mysqli_insert_id($conn);

            // If the question is radio or checkbox, insert the options
            if ($new_question_type == 'radio' || $new_question_type == 'checkbox') {
                if (isset($_POST['new_options'][$question_id])) {
                    foreach ($_POST['new_options'][$question_id] as $option) {
                        $option_text = mysqli_real_escape_string($conn, $option);
                        if (!empty($option_text)) {
                            $option_sql = "INSERT INTO options (question_id, option_text) VALUES ('$question_id', '$option_text')";
                            mysqli_query($conn, $option_sql);
                        }
                    }
                }
            }

            redirect("survey-edit.php?id=$id", 'Question added successfully!');
        } else {
            echo "Error adding question: " . mysqli_error($conn);
        }
    }

    // Handle updates for the remaining questions
foreach ($_POST['questions'] as $section_id => $questions_data) {
    foreach ($questions_data as $question_id => $question) {
        $question_text = mysqli_real_escape_string($conn, $question['text']);
        $type = mysqli_real_escape_string($conn, $question['type']);

        // Update the question in the `questions` table
        $sql = "UPDATE questions SET question = '$question_text', type = '$type' WHERE id = $question_id";
        mysqli_query($conn, $sql);

        // Update the options for radio/checkbox questions
        if ($type == 'radio' || $type == 'checkbox') {
            if (isset($_POST['options'][$question_id])) {
                $options_values = $_POST['options'][$question_id];
                // Delete existing options
                mysqli_query($conn, "DELETE FROM options WHERE question_id = $question_id");
                // Insert new options
                foreach ($options_values as $option) {
                    $option_text = mysqli_real_escape_string($conn, $option);
                    $sql = "INSERT INTO options (question_id, option_text) VALUES ($question_id, '$option_text')";
                    mysqli_query($conn, $sql);
                }
            }

            // Handle newly added options
            if (isset($_POST['new_options'][$question_id])) {
                foreach ($_POST['new_options'][$question_id] as $new_option) {
                    $new_option_text = mysqli_real_escape_string($conn, $new_option);
                    if (!empty($new_option_text)) {
                        $sql = "INSERT INTO options (question_id, option_text) VALUES ($question_id, '$new_option_text')";
                        mysqli_query($conn, $sql);
                    }
                }
            }
        }
    }
}

    redirect("survey-edit.php?id=$id", 'Survey edited successfully!');
exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Survey Questions</title>
</head>
<body>
<div class="row">
    <div class="col-md-12">
        <?php alertMessage() ?>
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit Survey Questions
                    <a href="survey.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php foreach ($sections as $section): ?>
                        <div class="section mb-4" data-section-id="<?php echo $section['id']; ?>">
                            <h5><?php echo htmlspecialchars($section['name']); ?>
                                <button type="submit" name="remove_section_id" value="<?php echo $section['id']; ?>" class="btn btn-danger float-end">Remove Section</button>
                            </h5>

                            <?php foreach ($questions[$section['id']] as $question): ?>
                                <div class="form-group mb-3">
                                    <label for="question-<?php echo $question['id']; ?>">Question:</label>
                                    <input 
                                        type="text" 
                                        id="question-<?php echo $question['id']; ?>" 
                                        name="questions[<?php echo $section['id']; ?>][<?php echo $question['id']; ?>][text]" 
                                        class="form-control" 
                                        value="<?php echo htmlspecialchars($question['question']); ?>" 
                                        required>
                                    <input type="hidden" name="questions[<?php echo $section['id']; ?>][<?php echo $question['id']; ?>][id]" value="<?php echo $question['id']; ?>">

                                    <label for="type-<?php echo $question['id']; ?>">Type:</label>
                                    <select id="type-<?php echo $question['id']; ?>" name="questions[<?php echo $section['id']; ?>][<?php echo $question['id']; ?>][type]" class="form-control">
                                        <option value="text" <?php if ($question['type'] == 'text') echo 'selected'; ?>>Text</option>
                                        <option value="radio" <?php if ($question['type'] == 'radio') echo 'selected'; ?>>Radio</option>
                                        <option value="checkbox" <?php if ($question['type'] == 'checkbox') echo 'selected'; ?>>Checkbox</option>
                                    </select>

                                    <button type="submit" name="remove_question_id" value="<?php echo $question['id']; ?>" class="btn btn-danger mt-2">Remove Question</button>

                                    <?php if ($question['type'] == 'radio' || $question['type'] == 'checkbox'): ?>
                                        <div class="options-container mt-2">
                                            <label>Options:</label>
                                            <?php foreach ($options[$question['id']] as $option): ?>
                                                <div class="option-item mb-2">
                                                    <input type="text" name="options[<?php echo $question['id']; ?>][]" class="form-control" value="<?php echo htmlspecialchars($option['option_text']); ?>">
                                                    <button type="submit" name="remove_option_id" value="<?php echo $option['id']; ?>" class="btn btn-danger btn-sm mt-2">Remove Option</button>
                                                </div>
                                            <?php endforeach; ?>
                                            <button type="button" class="btn btn-secondary mt-2 add-option" data-question-id="<?php echo $question['id']; ?>">Add Option</button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                            <button type="button" class="btn btn-secondary mt-3 add-question" data-section-id="<?php echo $section['id']; ?>">Add Question</button>
                        </div>

                    <?php endforeach; ?>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function () {
    function toggleOptions(selectElement) {
        const optionsContainer = selectElement.closest('.form-group').querySelector('.options-container');
        if (selectElement.value === 'radio' || selectElement.value === 'checkbox') {
            optionsContainer.classList.remove('d-none');
        } else {
            optionsContainer.classList.add('d-none');
        }
    }

    // Initialize existing dropdowns
    document.querySelectorAll('.question-type').forEach(select => {
        toggleOptions(select);
        select.addEventListener('change', function () {
            toggleOptions(this);
        });
    });

    // Event delegation for dynamically added "Add Option" buttons
    document.addEventListener('click', function (event) {
        if (event.target && event.target.classList.contains('add-option')) {
            const questionId = event.target.getAttribute('data-question-id');
            const optionsContainer = event.target.closest('.options-container');
            const newOptionHTML = `
                <div class="option-item mb-2">
                    <input type="text" name="new_options[${questionId}][]" class="form-control" placeholder="New Option">
                    <button type="button" name="remove_option_id" value="new" class="btn btn-danger btn-sm mt-2">Remove Option</button>
                </div>
            `;
            optionsContainer.insertAdjacentHTML('beforeend', newOptionHTML);
        }
    });

    // Event delegation for dynamically added "Remove Option" buttons
    document.addEventListener('click', function (event) {
        if (event.target && event.target.name === 'remove_option_id' && event.target.value === 'new') {
            const optionItem = event.target.closest('.option-item');
            if (optionItem) {
                optionItem.remove();
            }
        }
    });

    // Event delegation for dynamically added "Add Question" buttons
    document.addEventListener('click', function (event) {
        if (event.target && event.target.classList.contains('add-question')) {
            const sectionId = event.target.getAttribute('data-section-id');
            const sectionElement = event.target.closest('.section');
            const newQuestionHTML = `
                <div class="form-group mb-3 new-question">
                    <label for="question-new">Question:</label>
                    <input type="text" id="question-new" name="new_question_text" class="form-control" required>
                    <input type="hidden" name="section_id" value="${sectionId}">
                    <label for="type-new">Type:</label>
                    <select id="type-new" name="new_question_type" class="form-control question-type">
                        <option value="text">Text</option>
                        <option value="radio">Radio</option>
                        <option value="checkbox">Checkbox</option>
                    </select>
                    <div class="options-container mt-2 d-none">
                        <label>Options:</label>
                        <button type="button" class="btn btn-secondary mt-2 add-option" data-question-id="new">Add Option</button>
                    </div>
                    <button type="button" class="btn btn-danger mt-2 remove-question">Remove Question</button>
                </div>
            `;
            sectionElement.insertAdjacentHTML('beforeend', newQuestionHTML);

            // Add event listener for the newly added dropdown
            const newDropdown = sectionElement.querySelector('.new-question:last-child .question-type');
            newDropdown.addEventListener('change', function () {
                toggleOptions(this);
            });
        }
    });

    // Event delegation for dynamically added "Remove Question" buttons
    document.addEventListener('click', function (event) {
        if (event.target && event.target.classList.contains('remove-question')) {
            const questionItem = event.target.closest('.new-question');
            if (questionItem) {
                questionItem.remove();
            }
        }
    });
});

</script>
</body>
</html>
