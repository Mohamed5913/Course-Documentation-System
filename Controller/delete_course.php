<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Course.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);
    $db = new Database();
    $course = new Course($db);
    $course->deleteCourse($course_id);
    header("Location: ../View/instructor_dashboard.php?delete_success=1");
    exit();
}
?>