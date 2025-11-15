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
                <style>
                    /* Make section/question cards look nested inside the main card (Google Forms-like) */
                    #sections-container { margin-top: 12px; }
                    .section-card { box-shadow: 0 6px 18px rgba(0,0,0,0.06); border-radius: 8px; border: 1px solid #e9e9e9; }
                    .section-card .card-body { background: #ffffff; }
                    .questions-list .question-card { box-shadow: 0 2px 8px rgba(0,0,0,0.04); border-radius: 6px; margin-bottom: 10px; }
                    .drag-handle { font-size: 18px; color: #888; }
                    /* Ensure everything stays inside the parent card visually */
                    .card .section-card { margin-left: 0; margin-right: 0; }
                </style>
                <div class="form-group mb-3">
                    <label for="survey_name">Survey Name</label>
                    <input type="text" id="survey_name" name="survey_name" class="form-control" placeholder="Enter Survey Name" required>
                </div>

                <div class="form-group mb-3">
                    <label for="description">Survey Description</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Enter description" required></textarea>
                </div>

                <div id="sections-container" class="mb-3">
                    <!-- Section card template (first/default) -->
                    <div class="card section-card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">Section</h5>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm remove-section">Remove Section</button>
                                </div>
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
                                        <div class="drag-handle me-2" style="cursor:grab;">
                                            &#9776;
                                        </div>
                                        <div class="flex-fill">
                                            <div class="mb-2">
                                                <label>Question</label>
                                                <input type="text" name="questions[0][]" class="form-control question-input" placeholder="Enter Question" required>
                                            </div>

                                            <div class="mb-2">
                                                <label>Type</label>
                                                <select name="types[0][]" class="form-control question-type" onchange="toggleOptions(this)">
                                                    <option value="text">Text</option>
                                                    <option value="radio">Radio</option>
                                                    <option value="checkbox">Checkbox</option>
                                                </select>
                                            </div>

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
                </div>

                <button type="button" id="add-section" class="btn btn-secondary mt-3">Add Another Section</button>
                <button type="submit" class="btn btn-primary mt-3">Create Survey</button>
            </form>
        </div>
    </div>
</div>

<!-- Load SortableJS for drag-and-drop reordering -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Helper to update names/indexes after reorder/add/remove so server receives ordered arrays
function updateIndices() {
    const sections = document.querySelectorAll('.section-card');
    sections.forEach((sec, sIndex) => {
        // section_names[] and section_descriptions[] are simple arrays and will submit in DOM order
        // Update question and option input names
        const questions = sec.querySelectorAll('.question-card');
        questions.forEach((q, qIndex) => {
            const qInput = q.querySelector('.question-input');
            if (qInput) qInput.setAttribute('name', `questions[${sIndex}][]`);

            const typeSel = q.querySelector('.question-type');
            if (typeSel) typeSel.setAttribute('name', `types[${sIndex}][]`);

            // options
            const optionInputs = q.querySelectorAll('.option-item input');
            optionInputs.forEach(opt => {
                opt.setAttribute('name', `options[${sIndex}][${qIndex}][]`);
            });
        });
    });
}

function initializeSortables() {
    // Sections sortable
    const sectionsContainer = document.getElementById('sections-container');
    if (sectionsContainer && !sectionsContainer._sortable) {
        sectionsContainer._sortable = new Sortable(sectionsContainer, {
            handle: '.card-title',
            animation: 150,
            onEnd: function () { updateIndices(); }
        });
    }

    // Make each questions-list sortable
    document.querySelectorAll('.questions-list').forEach(function (ql) {
        if (!ql._sortable) {
            ql._sortable = new Sortable(ql, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function () { updateIndices(); }
            });
        }
    });
}

let sectionIndex = document.querySelectorAll('.section-card').length - 1;

document.getElementById('add-section').addEventListener('click', function () {
    sectionIndex++;
    const sectionTemplate = `
        <div class="card section-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title">Section</h5>
                    <div>
                        <button type="button" class="btn btn-danger btn-sm remove-section">Remove Section</button>
                    </div>
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
                                    <input type="text" name="questions[${sectionIndex}][]" class="form-control question-input" placeholder="Enter Question" required>
                                </div>

                                <div class="mb-2">
                                    <label>Type</label>
                                    <select name="types[${sectionIndex}][]" class="form-control question-type" onchange="toggleOptions(this)">
                                        <option value="text">Text</option>
                                        <option value="radio">Radio</option>
                                        <option value="checkbox">Checkbox</option>
                                    </select>
                                </div>

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
    initializeSortables();
    updateIndices();
});


// Remove a section
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-section')) {
        const sec = e.target.closest('.section-card');
        if (sec) sec.remove();
        updateIndices();
    }
});

// Add question functionality
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-question')) {
        const sec = e.target.closest('.section-card');
        const questionsContainer = sec.querySelector('.questions-list');
        const sectionIndex = [...document.querySelectorAll('.section-card')].indexOf(sec); // Get the section index
        const questionIndex = questionsContainer.querySelectorAll('.question-card').length; // Get the number of questions in the section

        const questionTemplate = `
            <div class="card question-card mt-3 mb-2">
                <div class="card-body d-flex align-items-start">
                    <div class="drag-handle me-2" style="cursor:grab;">&#9776;</div>
                    <div class="flex-fill">
                        <div class="mb-2">
                            <label>Question</label>
                            <input type="text" name="questions[${sectionIndex}][]" class="form-control question-input" placeholder="Enter Question" required>
                        </div>

                        <div class="mb-2">
                            <label>Type</label>
                            <select name="types[${sectionIndex}][]" class="form-control question-type" onchange="toggleOptions(this)">
                                <option value="text">Text</option>
                                <option value="radio">Radio</option>
                                <option value="checkbox">Checkbox</option>
                            </select>
                        </div>

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
                    <div class="ms-2">
                        <button type="button" class="btn btn-danger btn-sm remove-question">Remove</button>
                    </div>
                </div>
            </div>
        `;
        questionsContainer.insertAdjacentHTML('beforeend', questionTemplate);
        initializeSortables();
        updateIndices();
    }
});

// Remove a question
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-question')) {
        const q = e.target.closest('.question-card');
        if (q) q.remove();
        updateIndices();
    }
});

// Toggle options based on question type
function toggleOptions(selectElement) {
    const qCard = selectElement.closest('.question-card');
    const optionsContainer = qCard ? qCard.querySelector('.options-container') : null;
    if (!optionsContainer) return;
    if (selectElement.value === 'radio' || selectElement.value === 'checkbox') {
        optionsContainer.classList.remove('d-none');
    } else {
        optionsContainer.classList.add('d-none');
    }
}

// Add option dynamically
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-option')) {
        const optionsList = e.target.parentElement.querySelector('.options-list'); // Get the options list
        const questionCard = e.target.closest('.question-card');
        const sec = questionCard.closest('.section-card');
        const sectionIndex = [...document.querySelectorAll('.section-card')].indexOf(sec);
        const questionIndex = [...sec.querySelectorAll('.question-card')].indexOf(questionCard);

        const optionTemplate = `
            <div class="option-item d-flex align-items-center mb-2">
                <input type="text" name="options[${sectionIndex}][${questionIndex}][]" class="form-control me-2" placeholder="Enter Option">
                <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
            </div>
        `;
        optionsList.insertAdjacentHTML('beforeend', optionTemplate);
        updateIndices();
    }
});

// Remove an option
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-option')) {
        e.target.closest('.option-item').remove();
        updateIndices();
    }
});

// initialize sortables on load
initializeSortables();
updateIndices();
</script>
