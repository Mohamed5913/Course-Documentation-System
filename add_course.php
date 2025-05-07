<?php
session_start();
require_once 'Database.php';
require_once 'Course.php';
require_once 'Subject.php';
require_once 'CourseCreationObserver.php';
require_once 'LoggingObserver.php';

$db = new Database();
$course = new Course($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $instructor_id = $_SESSION['id'];

    if ($course->addCourse($course_name, $description, $instructor_id)) {
        // Observer pattern integration
        $subject = new Subject();
        $subject->attach(new CourseCreationObserver());
        $subject->attach(new LoggingObserver());
        $subject->notify([
            'message' => "New course created: $course_name (Instructor ID: $instructor_id)",
            'course_name' => $course_name,