<?php
ob_start(); // Start output buffering

include('includes/header.php');
include('../config/db_connect.php'); // Include the database connection

// Get all surveys for listing
$surveys = mysqli_query($conn, "SELECT * FROM surveys");
?>
<?= alertmessage(); ?>

<div class="card mt-4">
    <div class="card-header">
        <h4>
            Existing Surveys
            <a href="survey-create.php" class="btn btn-primary float-end">Add Survey</a>
        </h4>
    </div>
    <div class="card-body">
        <table id="surveyTable" class="table table-bordered table-striped text-center">
            <thead>
                <tr>
                    <th>Survey ID</th>
                    <th>Survey Name</th>
                    <th>Questions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($surveys)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td>
                        <?php
                            // Fetch sections for the survey
                            $survey_id = $row['id'];
                            $sections_query = "SELECT * FROM sections WHERE survey_id = $survey_id";
                            $sections_result = mysqli_query($conn, $sections_query);
                            $total_questions = 0;

                            // Loop through sections to count questions
                            while ($section = mysqli_fetch_assoc($sections_result)) {
                                $section_id = $section['id'];
                                $questions_query = "SELECT * FROM questions WHERE section_id = $section_id";
                                $questions_result = mysqli_query($conn, $questions_query);
                                $total_questions += mysqli_num_rows($questions_result);
                            }
                            echo $total_questions . " questions";
                        ?>
                    </td>
                    <td>
                        <a href="responses.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">View</a>
                        <a href="survey-edit.php?id=<?= $row['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                        <a href="survey-delete.php?delete=<?= $row['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this survey?')">Delete</a>
                        <?php alertMessage()?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('add-question').addEventListener('click', function() {
        var container = document.getElementById('questions-container');
        var questionDiv = document.createElement('div');
        questionDiv.classList.add('form-group', 'mt-3');
        questionDiv.innerHTML = `
            <label>Question</label>
            <input type="text" name="questions[]" class="form-control" placeholder="Enter Question" required>

            <label>Type</label>
            <select name="types[]" class="form-control">
                <option value="text">Text</option>
                <option value="radio">Radio</option>
                <option value="checkbox">Checkbox</option>
            </select>
        `;
        container.appendChild(questionDiv);
    });
</script> 

<?php
// End output buffering and flush the output
ob_end_flush();
?>
