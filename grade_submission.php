<?php
session_start();
require_once 'Database.php';
require_once 'Submission.php';
require_once 'Subject.php';
require_once 'QuizAssignmentResultObserver.php';
require_once 'LoggingObserver.php';

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$submissionModel = new Submission($db);

// Handle grade submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_id = $_POST['submission_id'];
    $grade = $_POST['grade'];

    // Validate grade input
    if (is_numeric($grade) && $grade >= 0 && $grade <= 100) {
        $submissionModel->gradeSubmission($submission_id, $grade);

        // Observer pattern: notify observers
        $subject = new Subject();
        $subject->attach(new QuizAssignmentResultObserver());
        $subject->attach(new LoggingObserver());
        $subject->notify([
            'message' => "Assignment graded: Submission ID $submission_id, Grade: $grade",
            'type' => 'assignment',
            'submission_id' => $submission_id,
            'grade' => $grade,
            'instructor_id' => $_SESSION['id']
        ]);

        // Redirect back to the course details page
        header("Location: course_details.php?id=" . $_GET['id']);
        exit();
    } else {
        echo "Invalid grade. Please enter a value between 0 and 100.";
    }
}
?>