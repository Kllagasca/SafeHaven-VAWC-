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

                <style>
                    /* Nesting styles (match survey-create) */
                    .section-card { box-shadow: 0 6px 18px rgba(0,0,0,0.06); border-radius: 8px; border: 1px solid #e9e9e9; }
                    .section-card .card-body { background: #ffffff; }
                    .questions-list .question-card { box-shadow: 0 2px 8px rgba(0,0,0,0.04); border-radius: 6px; margin-bottom: 10px; }
                    .drag-handle { font-size: 18px; color: #888; cursor: grab; }
                </style>

                <form method="POST">
                    <div id="sections-container">
                    <?php foreach ($sections as $section): ?>
                        <div class="card section-card mb-3" data-section-id="<?php echo $section['id']; ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title"><?php echo htmlspecialchars($section['name']); ?></h5>
                                    <form method="POST" style="margin:0;">
                                        <button type="submit" name="remove_section_id" value="<?php echo $section['id']; ?>" class="btn btn-danger btn-sm">Remove Section</button>
                                    </form>
                                </div>

                                <div class="questions-list">
                                    <?php foreach ($questions[$section['id']] as $question): ?>
                                        <div class="card question-card mb-2">
                                            <div class="card-body d-flex align-items-start">
                                                <div class="drag-handle me-2">&#9776;</div>
                                                <div class="flex-fill">
                                                    <label for="question-<?php echo $question['id']; ?>">Question:</label>
                                                    <input 
                                                        type="text" 
                                                        id="question-<?php echo $question['id']; ?>" 
                                                        name="questions[<?php echo $section['id']; ?>][<?php echo $question['id']; ?>][text]" 
                                                        class="form-control" 
                                                        value="<?php echo htmlspecialchars($question['question']); ?>" 
                                                        required>
                                                    <input type="hidden" name="questions[<?php echo $section['id']; ?>][<?php echo $question['id']; ?>][id]" value="<?php echo $question['id']; ?>">

                                                    <div class="mt-2">
                                                        <label for="type-<?php echo $question['id']; ?>">Type:</label>
                                                        <select id="type-<?php echo $question['id']; ?>" name="questions[<?php echo $section['id']; ?>][<?php echo $question['id']; ?>][type]" class="form-control question-type">
                                                            <option value="text" <?php if ($question['type'] == 'text') echo 'selected'; ?>>Text</option>
                                                            <option value="radio" <?php if ($question['type'] == 'radio') echo 'selected'; ?>>Radio</option>
                                                            <option value="checkbox" <?php if ($question['type'] == 'checkbox') echo 'selected'; ?>>Checkbox</option>
                                                        </select>
                                                    </div>

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
                                                <div class="ms-2">
                                                    <form method="POST" style="margin:0;">
                                                        <button type="submit" name="remove_question_id" value="<?php echo $question['id']; ?>" class="btn btn-danger">Remove</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mt-3">
                                    <button type="button" class="btn btn-secondary btn-sm add-question" data-section-id="<?php echo $section['id']; ?>">Add Question</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>

                    <div class="form-group mt-3">
                        <button type="button" id="add-section" class="btn btn-secondary">Add Another Section</button>
                        <button type="submit" class="btn btn-primary ms-2">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function () {
    // Toggle options: support both legacy .form-group and new .question-card layouts
    function toggleOptions(selectElement) {
        let container = null;
        const qCard = selectElement.closest('.question-card');
        if (qCard) {
            container = qCard.querySelector('.options-container');
        } else {
            container = selectElement.closest('.form-group') ? selectElement.closest('.form-group').querySelector('.options-container') : null;
        }
        if (!container) return;
        if (selectElement.value === 'radio' || selectElement.value === 'checkbox') {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
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
            const sectionCard = event.target.closest('.section-card');
            if (!sectionCard) return;
            const questionsList = sectionCard.querySelector('.questions-list');
            const newQuestionHTML = `
                <div class="card question-card mb-2 new-question">
                    <div class="card-body d-flex align-items-start">
                        <div class="drag-handle me-2">&#9776;</div>
                        <div class="flex-fill">
                            <label for="question-new">Question:</label>
                            <input type="text" id="question-new" name="new_question_text" class="form-control" required>
                            <input type="hidden" name="section_id" value="${sectionId}">

                            <div class="mt-2">
                                <label for="type-new">Type:</label>
                                <select id="type-new" name="new_question_type" class="form-control question-type">
                                    <option value="text">Text</option>
                                    <option value="radio">Radio</option>
                                    <option value="checkbox">Checkbox</option>
                                </select>
                            </div>

                            <div class="options-container mt-2 d-none">
                                <label>Options:</label>
                                <div class="options-list"></div>
                                <button type="button" class="btn btn-secondary mt-2 add-option" data-question-id="new">Add Option</button>
                            </div>
                            <button type="button" class="btn btn-danger mt-2 remove-question">Remove Question</button>
                        </div>
                        <div class="ms-2">
                            <!-- placeholder for remove button in existing items -->
                        </div>
                    </div>
                </div>
            `;
            questionsList.insertAdjacentHTML('beforeend', newQuestionHTML);

            // Add event listener for the newly added dropdown
            const newDropdown = questionsList.querySelector('.new-question:last-child .question-type');
            if (newDropdown) {
                newDropdown.addEventListener('change', function () {
                    toggleOptions(this);
                });
            }
        }
    });

    // Event delegation for dynamically added "Remove Question" buttons
    document.addEventListener('click', function (event) {
        if (event.target && event.target.classList.contains('remove-question')) {
            const questionItem = event.target.closest('.new-question');
            if (questionItem) {
                questionItem.remove();
            } else {
                // If it's an existing question card in edit mode, remove the whole question-card
                const q = event.target.closest('.question-card');
                if (q) q.remove();
            }
        }
    });

    // Client-side remove section for newly added sections
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-section')) {
            const sec = e.target.closest('.section-card');
            if (sec) sec.remove();
        }
    });

    // Add Another Section - inserts a new section card (client-side only)
    let sectionIndex = document.querySelectorAll('.section-card').length;
    const addSectionBtn = document.getElementById('add-section');
    if (addSectionBtn) {
        addSectionBtn.addEventListener('click', function () {
            const sectionTemplate = `
                <div class="card section-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">Section</h5>
                            <button type="button" class="btn btn-danger btn-sm remove-section">Remove Section</button>
                        </div>

                        <div class="mb-3">
                            <label>Section Name</label>
                            <input type="text" name="section_names[]" class="form-control section-name" placeholder="Enter Section Name" required>
                        </div>

                        <div class="mb-3">
                            <label>Section Description</label>
                            <textarea name="section_descriptions[]" class="form-control section-desc" placeholder="Enter Section Description" required></textarea>
                        </div>

                        <div class="questions-list">
                            <div class="card question-card mb-2">
                                <div class="card-body d-flex align-items-start">
                                    <div class="drag-handle me-2" style="cursor:grab;">&#9776;</div>
                                    <div class="flex-fill">
                                        <div class="mb-2">
                                            <label>Question</label>
                                            <input type="text" name="questions_new[${sectionIndex}][]" class="form-control question-input" placeholder="Enter Question" required>
                                        </div>

                                        <div class="mb-2">
                                            <label>Type</label>
                                            <select name="types_new[${sectionIndex}][]" class="form-control question-type">
                                                <option value="text">Text</option>
                                                <option value="radio">Radio</option>
                                                <option value="checkbox">Checkbox</option>
                                            </select>
                                        </div>

                                        <div class="options-container d-none mt-2">
                                            <label>Options</label>
                                            <div class="options-list">
                                                <div class="option-item d-flex align-items-center mb-2">
                                                    <input type="text" name="options_new[${sectionIndex}][0][]" class="form-control me-2" placeholder="Enter Option">
                                                    <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-sm add-option">Add Option</button>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-question">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-secondary btn-sm add-question">Add Another Question</button>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('sections-container').insertAdjacentHTML('beforeend', sectionTemplate);
            // Attach change listener to newly inserted question-type so toggleOptions works
            const container = document.getElementById('sections-container');
            const newSec = container.querySelector('.section-card:last-child');
            if (newSec) {
                const newType = newSec.querySelector('.question-type');
                if (newType) newType.addEventListener('change', function () { toggleOptions(this); });
            }
            sectionIndex++;
        });
    }

});

</script>

</body>
</html>
