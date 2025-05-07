<?php
session_start();
require_once 'Database.php';
require_once 'FileUpload.php';
require_once 'MaterialUploadSubject.php';
require_once 'LoggingObserver.php';
require_once 'MaterialUploadObserver.php';

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$db = new Database();
// Use the LoggingFileUpload decorator
$fileUpload = new LoggingFileUpload(new FileUpload());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['material'])) {
        $course_id = $_POST['course_id'];
        $instructor_id = $_SESSION['id'];
        $material_title = $_POST['material_title'];
        $file = $_FILES['material'];

        $target_dir = "uploads/materials/";
        $file_path = $fileUpload->uploadFile($file, $target_dir);

        if ($file_path) {
            $insert_sql = "INSERT INTO course_materials (course_id, instructor_id, material_path, material_title) VALUES (?, ?, ?, ?)";
            $insert_stmt = $db->getConnection()->prepare($insert_sql);
            $insert_stmt->bind_param("iiss", $course_id, $instructor_id, $file_path, $material_title);
            $insert_stmt->execute();
        

        // Notify all students enrolled in the course
        $students_sql = "SELECT student_id FROM course_registrations WHERE course_id = ?";
        $students_stmt = $db->getConnection()->prepare($students_sql);
        $students_stmt->bind_param("i", $course_id);
        $students_stmt->execute();
        $students_result = $students_stmt->get_result();

        $notification_sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $notification_stmt = $db->getConnection()->prepare($notification_sql);
        $notification_message = "New material uploaded: $material_title";

        while ($student = $students_result->fetch_assoc()) {
            $notification_stmt->bind_param("is", $student['student_id'], $notification_message);
            $notification_stmt->execute();
        }

        // Observer pattern: notify observers
        $subject = new MaterialUploadSubject();
        $subject->attach(new LoggingObserver());
        $subject->attach(new MaterialUploadObserver());
        $subject->notify([
            'message' => "Material uploaded: $material_title (Course ID: $course_id)",
            'material_title' => $material_title,
            'instructor_id' => $instructor_id
        ]);
    }


            echo "Material uploaded successfully!";
        } else {
            echo "Failed to upload material.";
        }
    }
?>