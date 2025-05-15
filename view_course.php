<?php
session_start();
require_once 'Database.php';
require_once 'Course.php';
require_once 'Assignment.php';
require_once 'Quiz.php';
require_once 'Material.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$course = new Course($db);
$assignment = new Assignment($db);
$quiz = new Quiz($db);

$course_id = $_GET['id'];
$user_id = $_SESSION['id'];
$role = $_SESSION['role'];

// Fetch course details
$course_details = $course->getCourseDetails($course_id);

// Fetch quizzes for the course
$quizzes = $quiz->getQuizzesByCourse($course_id);

// Fetch assignments for the course
$assignments = $assignment->getAssignmentsByCourse($course_id);
$student_submissions = [];
$submission_stmt = $db->getConnection()->prepare(
    "SELECT assignment_id, grade FROM submissions WHERE student_id = ? AND assignment_id = ?"
);

$assignments_array = [];
while ($assignment = $assignments->fetch_assoc()) {
    $assignments_array[] = $assignment;
}
$assignments = $assignments_array;

foreach ($assignments as &$assignment) {
    $submission_stmt->bind_param("ii", $user_id, $assignment['id']);
    $submission_stmt->execute();
    $result = $submission_stmt->get_result();
    $submission = $result->fetch_assoc();
    $assignment['student_grade'] = $submission ? $submission['grade'] : null;
}

$material = new Material($db);
$materials = $material->getMaterialsByCourse($course_id);

// Handle assignment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['assignment_file']) && isset($_POST['assignment_id'])) {
    $assignment_id = $_POST['assignment_id'];
    $file_name = $_FILES['assignment_file']['name'];
    $file_tmp = $_FILES['assignment_file']['tmp_name'];
    $file_path = "uploads/assignments/" . basename($file_name);

    if (move_uploaded_file($file_tmp, $file_path)) {
        $assignment->submitAssignment($assignment_id, $user_id, $file_path, $file_name);
        $success_message = "Assignment submitted successfully!";
    } else {
        $success_message = "Failed to upload the file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course</title>
    <link rel="stylesheet" href="view_course.css">
</head>
<body>
    <header>
        <h1 class="course-title"><?= htmlspecialchars($course_details['course_name']) ?></h1>
    </header>

    <main class="course-main">
        <!-- Go Back to Dashboard Button -->
        <div class="dashboard-btn-container">
            <button onclick="window.location.href='<?= ($role === 'instructor') ? 'instructor_dashboard.php' : 'student_dashboard.php' ?>'" class="dashboard-btn">
                Go Back to Dashboard
            </button>
        </div>

        <p class="course-description"><?= htmlspecialchars($course_details['description']) ?></p>


        <!-- Display Course Materials -->
        <section class="materials-section">
            <h3 class="section-title">Course Materials</h3>
            <?php if ($materials && $materials->num_rows > 0): ?>
                <ul class="materials-list">
                    <?php while ($material = $materials->fetch_assoc()): ?>
                        <li class="material-item">
                            <a href="<?= htmlspecialchars($material['material_path']) ?>" download>
                                <?= htmlspecialchars($material['material_title']) ?>
                            </a>
                            <?php if (!empty($material['description'])): ?>
                                <p><?= htmlspecialchars($material['description']) ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="no-materials">No materials uploaded for this course.</p>
            <?php endif; ?>
        </section>

        <!-- Display Quizzes -->
        <section class="course-section">
            <h3 class="section-title">Quizzes</h3>
            <?php if ($quizzes->num_rows > 0): ?>
                <ul class="quiz-list">
                    <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                        <li class="quiz-item">
                            <a href="take_quiz.php?quiz_id=<?= $quiz['id'] ?>&course_id=<?= $course_id ?>">
                                <?= htmlspecialchars($quiz['title']) ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="no-items">No quizzes available for this course.</p>
            <?php endif; ?>
        </section>

        <!-- Display Assignments -->
        <section class="assignments-section">
            <h3 class="section-title">Assignments</h3>
            <?php if (!empty($assignments)): ?>
                <ul>
                    <?php foreach ($assignments as $assignment): ?>
                        <li class="assignment-item">
                            <h4 class="assignment-name"><?= htmlspecialchars($assignment['assignment_title']) ?></h4>
                            <p class="assignment-details"><?= htmlspecialchars($assignment['assignment_details']) ?></p>
                            <p class="due-date"><strong>Due Date:</strong> <?= htmlspecialchars($assignment['due_date']) ?></p>

                            <!-- Download Assignment File -->
                            <?php if (!empty($assignment['assignment_file'])): ?>
                                <a href="<?= htmlspecialchars($assignment['assignment_file']) ?>" download class="download-btn">
                                    Download Assignment
                                </a>
                            <?php else: ?>
                                <p>No file uploaded for this assignment.</p>
                            <?php endif; ?>

                            <!-- Assignment Submission Form -->
                            <form action="view_course.php?id=<?= $course_id ?>" method="POST" enctype="multipart/form-data" class="assignment-form">
                                <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                <label for="assignment_file">Submit your assignment:</label>
                                <input type="file" name="assignment_file" id="assignment_file" required>
                                <button type="submit">Submit Assignment</button>
                            </form>

                            <?php if ($role === 'student' && $assignment['student_grade'] !== null): ?>
                                <p class="assignment-grade"><strong>Your Grade:</strong> <?= htmlspecialchars($assignment['student_grade']) ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (isset($success_message)): ?>
                    <div class="success-message"><?= $success_message ?></div>
                <?php endif; ?>
            <?php else: ?>
                <p class="no-assignments">No assignments available for this course.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>