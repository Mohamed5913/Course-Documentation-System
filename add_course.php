<?php
session_start();
require_once 'Database.php';
require_once 'Course.php';

$db = new Database();
$course = new Course($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $instructor_id = $_SESSION['id'];

    if ($course->addCourse($course_name, $description, $instructor_id)) {
        header("Location: instructor_dashboard.php");
        exit();
    } else {
        echo "Error adding course.";
    }
}
?>