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
    $assignment_id = isset($_GET['id']) ? $_GET['id'] : null;

    // Validate grade input
    if (is_numeric($grade) && $grade >= 0 && $grade <= 100) {
        $submissionModel->gradeSubmission($submission_id, $grade);

         // Get the student_id for this submission
        $submission = $submissionModel->getSubmission($submission_id);
        $student_id = $submission['student_id'];

        // Insert notification for the student
        $notif_stmt = $db->getConnection()->prepare(
            "INSERT INTO notifications (user_id, user_role, message, is_read, created_at) VALUES (?, 'student', ?, 0, NOW())"
        );
        $notif_message = "Your assignment has been graded. Grade: $grade";
        $notif_stmt->bind_param("is", $student_id, $notif_message);
        $notif_stmt->execute();

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

        // Redirect back to the submissions page for this assignment
        if ($assignment_id) {
            header("Location: submissions.php?assignment_id=" . urlencode($assignment_id));
        } else {
            header("Location: instructor_dashboard.php");
        }
        exit();
    } else {
        echo "Invalid grade. Please enter a value between 0 and 100.";
    }
}
?>