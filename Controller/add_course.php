<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Course.php';
require_once '../Model/Subject.php';
require_once '../Model/CourseCreationObserver.php';
require_once '../Model/LoggingObserver.php';

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
            'instructor_id' => $instructor_id
        ]);
        // Redirect or success message
        header("Location: ../View/instructor_dashboard.php?success=1");
        exit();
    } else {
        // Handle failure
        header("Location: ../View/courses.php?error=1");
        exit();
    }
}
?>