<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Course.php';

$db = new Database();
$courseModel = new Course($db);

if ($_SESSION['role'] === 'instructor') {
    $result = $courseModel->getCoursesByInstructor($_SESSION['id']);
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($course = $result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($course['course_name']) . " - " . htmlspecialchars($course['description']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No courses added yet.";
    }
} else if ($_SESSION['role'] === 'student') {
    $result = $courseModel->getCoursesByStudent($_SESSION['id']);
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($course = $result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($course['course_name']) . " - " . htmlspecialchars($course['description']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "You are not registered for any courses.";
    }
} else {
    echo "Invalid user role.";
}
?>
