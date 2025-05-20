<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Assignment.php';

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$assignment = new Assignment($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $assignment_title = $_POST['assignment_name'];
    $assignment_details = $_POST['assignment_details'];
    $due_date = $_POST['due_date'];

    if ($assignment->addAssignment($course_id, $assignment_title, $assignment_details, $due_date)) {
        header("Location: ../View/course_details.php?id=$course_id");
        exit();
    } else {
        echo "Error adding assignment.";
    }
}
?>
