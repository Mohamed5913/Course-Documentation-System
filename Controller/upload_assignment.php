<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Assignment.php';

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_assignment'])) {
    $course_id = $_POST['course_id'];
    $assignment_title = $_POST['assignment_title'];
    $assignment_details = $_POST['assignment_details'];
    $due_date = $_POST['due_date'];
    $instructor_id = $_SESSION['id'];

    // Handle file upload
    $file_name = $_FILES['assignment_file']['name'];
    $file_tmp = $_FILES['assignment_file']['tmp_name'];
    $upload_dir = "../uploads/assignments/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $file_path = $upload_dir . basename($file_name);

    if (move_uploaded_file($file_tmp, $file_path)) {
        $db = new Database();
        $assignment = new Assignment($db);
        $assignment->addAssignment($course_id, $assignment_title, $assignment_details, $due_date, $file_path, $file_name, $instructor_id);
        header("Location: ../View/course_details.php?id=$course_id&success=1");
        exit();
    } else {
        header("Location: ../View/course_details.php?id=$course_id&error=upload");
        exit();
    }
}
?>