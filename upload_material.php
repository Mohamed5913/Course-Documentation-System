<?php
session_start();
require_once 'Database.php';
require_once 'FileUpload.php';

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$fileUpload = new FileUpload();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['material'])) {
        $course_id = $_POST['course_id'];
        $instructor_id = $_SESSION['id'];
        $material_title = $_POST['material_title'];
        $file = $_FILES['material'];

        $target_dir = "uploads/materials/";
        $file_path = $fileUpload->uploadFile($file, $target_dir);

        if ($file_path) {
            $sql = "INSERT INTO course_materials (course_id, instructor_id, material_path, material_title) VALUES (?, ?, ?, ?)";
            $stmt = $db->getConnection()->prepare($sql);
            $stmt->bind_param("iiss", $course_id, $instructor_id, $file_path, $material_title);
            $stmt->execute();
            echo "Material uploaded successfully!";
        } else {
            echo "Failed to upload material.";
        }
    }
}
?>