<?php
ob_start(); // Start output buffering
include('../config/db_connect.php');
include 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['survey_name'])) {
    $survey_name = mysqli_real_escape_string($conn, $_POST['survey_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Insert survey
    $sql = "INSERT INTO surveys (name, description) VALUES ('$survey_name', '$description')";
    if (!mysqli_query($conn, $sql)) {
        echo "Survey Insert Error: " . mysqli_error($conn);
        exit;
    }
    $survey_id = mysqli_insert_id($conn);

    // Insert sections
    foreach ($_POST['section_names'] as $section_index => $section_name) {
        $section_name = mysqli_real_escape_string($conn, $section_name);
        $section_description = mysqli_real_escape_string($conn, $_POST['section_descriptions'][$section_index]);

        $sql = "INSERT INTO sections (survey_id, name, description) VALUES ('$survey_id', '$section_name', '$section_description')";
        if (!mysqli_query($conn, $sql)) {
            echo "Section Insert Error: " . mysqli_error($conn);
            exit;
        }
        $section_id = mysqli_insert_id($conn);

        // Insert questions for this section
        if (isset($_POST['questions'][$section_index])) {
            foreach ($_POST['questions'][$section_index] as $question_index => $question) {
                $question = mysqli_real_escape_string($conn, $question);
                $type = mysqli_real_escape_string($conn, $_POST['types'][$section_index][$question_index]);

                if (!empty($question) && !empty($type)) {
                    $sql = "INSERT INTO questions (survey_id,section_id, question, type) VALUES ('$survey_id','$section_id', '$question', '$type')";
                    if (!mysqli_query($conn, $sql)) {
                        echo "Question Insert Error: " . mysqli_error($conn);
                        exit;
                    }
                    $question_id = mysqli_insert_id($conn);

                    // Insert options for radio/checkbox questions
                    if (($type === 'radio' || $type === 'checkbox') && isset($_POST['options'][$section_index][$question_index])) {
                        foreach ($_POST['options'][$section_index][$question_index] as $option) {
                            $option = mysqli_real_escape_string($conn, $option);
                            if (!empty($option)) {
                                $sql = "INSERT INTO options (question_id, option_text) VALUES ('$question_id', '$option')";
                                if (!mysqli_query($conn, $sql)) {
                                    echo "Option Insert Error: " . mysqli_error($conn);
                                    exit;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $_SESSION['message'] = "Survey created successfully!";
    redirect('survey.php', 'Survey created successfully!');
}

ob_end_flush(); // End output buffering
?>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4>
                Create Survey
                <a href="survey.php" class="btn btn-primary float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">
            <?php alertMessage() ?>
            <form method="POST">
                <div class="form-group mb-3">
                    <label for="survey_name">Survey Name</label>
                    <input type="text" id="survey_name" name="survey_name" class="form-control" placeholder="Enter Survey Name" required>
                </div>

                <div class="form-group mb-3">
                    <label for="description">Survey Description</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Enter description" required></textarea>
                </div>

                <div id="sections-container">
                    <div class="section form-group mb-4">
                        <h5>Section</h5>
                        <label for="section_name">Section Name</label>
                        <input type="text" name="section_names[]" class="form-control" placeholder="Enter Section Name" required>

                        <label for="section_description">Section Description</label>
                        <textarea name="section_descriptions[]" class="form-control" placeholder="Enter Section Description" required></textarea>

                        <div class="questions-container mt-3">
                            <div class="question form-group">
                                <label>Question</label>
                                <input type="text" name="questions[0][]" class="form-control" placeholder="Enter Question" required>

                                <label>Type</label>
                                <select name="types[0][]" class="form-control question-type" onchange="toggleOptions(this)">
                                    <option value="text">Text</option>
                                    <option value="radio">Radio</option>
                                    <option value="checkbox">Checkbox</option>
                                </select>

                                <div class="options-container d-none mt-2">
                                    <label>Options</label>
                                    <div class="options-list">
                                        <div class="option-item d-flex align-items-center mb-2">
                                            <input type="text" name="options[0][0][]" class="form-control me-2" placeholder="Enter Option">
                                            <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm add-option">Add Option</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary mt-3 add-question">Add Another Question</button>
                        <button type="button" class="btn btn-danger mt-3 remove-section">Remove Section</button>
                    </div>
                </div>

                <button type="button" id="add-section" class="btn btn-secondary mt-3">Add Another Section</button>
                <button type="submit" class="btn btn-primary mt-3">Create Survey</button>
            </form>
        </div>
    </div>
</div>
<script>
let sectionIndex = 0; // Start with 0 for the first section.

document.getElementById('add-section').addEventListener('click', function () {
    sectionIndex++;
    const sectionTemplate = `
        <div class="section form-group mb-4">
            <h5>Section</h5>
            <label for="section_name">Section Name</label>
            <input type="text" name="section_names[]" class="form-control" placeholder="Enter Section Name" required>

            <label for="section_description">Section Description</label>
            <textarea name="section_descriptions[]" class="form-control" placeholder="Enter Section Description" required></textarea>

            <div class="questions-container mt-3">
                <div class="question form-group">
                    <label>Question</label>
                    <input type="text" name="questions[${sectionIndex}][]" class="form-control" placeholder="Enter Question" required>

                    <label>Type</label>
                    <select name="types[${sectionIndex}][]" class="form-control question-type" onchange="toggleOptions(this)">
                        <option value="text">Text</option>
                        <option value="radio">Radio</option>
                        <option value="checkbox">Checkbox</option>
                    </select>

                    <div class="options-container d-none mt-2">
                        <label>Options</label>
                        <div class="options-list">
                            <div class="option-item d-flex align-items-center mb-2">
                                <input type="text" name="options[${sectionIndex}][0][]" class="form-control me-2" placeholder="Enter Option">
                                <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm add-option">Add Option</button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary mt-3 add-question">Add Another Question</button>
            <button type="button" class="btn btn-danger mt-3 remove-section">Remove Section</button>
        </div>
    `;
    document.getElementById('sections-container').insertAdjacentHTML('beforeend', sectionTemplate);
});


// Remove a section
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-section')) {
        e.target.closest('.section').remove();
    }
});

// Add question functionality
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-question')) {
        const section = e.target.closest('.section');
        const questionsContainer = section.querySelector('.questions-container');
        const sectionIndex = [...document.querySelectorAll('.section')].indexOf(section); // Get the section index
        const questionIndex = questionsContainer.querySelectorAll('.question').length; // Get the number of questions in the section

        const questionTemplate = `
            <div class="question form-group mt-3">
                <label>Question</label>
                <input type="text" name="questions[${sectionIndex}][]" class="form-control" placeholder="Enter Question" required>

                <label>Type</label>
                <select name="types[${sectionIndex}][]" class="form-control question-type" onchange="toggleOptions(this)">
                    <option value="text">Text</option>
                    <option value="radio">Radio</option>
                    <option value="checkbox">Checkbox</option>
                </select>

                <div class="options-container d-none mt-2">
                    <label>Options</label>
                    <div class="options-list">
                        <div class="option-item d-flex align-items-center mb-2">
                            <input type="text" name="options[${sectionIndex}][${questionIndex}][]" class="form-control me-2" placeholder="Enter Option">
                            <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm add-option">Add Option</button>
                </div>
            </div>
        `;
        questionsContainer.insertAdjacentHTML('beforeend', questionTemplate);
    }
});

// Remove a question
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-question')) {
        e.target.closest('.question').remove();
    }
});

// Toggle options based on question type
function toggleOptions(selectElement) {
    const optionsContainer = selectElement.closest('.question').querySelector('.options-container');
    if (selectElement.value === 'radio' || selectElement.value === 'checkbox') {
        optionsContainer.classList.remove('d-none');
    } else {
        optionsContainer.classList.add('d-none');
    }
}

// Add option dynamically
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-option')) {
        const optionsList = e.target.previousElementSibling; // Get the options list
        const question = e.target.closest('.question');
        const sectionIndex = [...document.querySelectorAll('.section')].indexOf(question.closest('.section')); // Get the section index
        const questionIndex = [...question.closest('.questions-container').querySelectorAll('.question')].indexOf(question); // Get the question index

        const optionTemplate = `
            <div class="option-item d-flex align-items-center mb-2">
                <input type="text" name="options[${sectionIndex}][${questionIndex}][]" class="form-control me-2" placeholder="Enter Option">
                <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
            </div>
        `;
        optionsList.insertAdjacentHTML('beforeend', optionTemplate);
    }
});

// Remove an option
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-option')) {
        e.target.closest('.option-item').remove();
    }
});

</script>
