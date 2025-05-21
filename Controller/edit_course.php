<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Course.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];

    $db = new Database();
    $course = new Course($db);
    $course->updateCourse($course_id, $course_name, $description);

    header("Location: ../View/instructor_dashboard.php?edit_success=1");
    exit();
}
?>