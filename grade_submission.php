<?php
session_start();
include 'db.php';  // Ensure you're connected to the database

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

// Handle grade submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_id = $_POST['submission_id'];
    $grade = $_POST['grade'];

    // Validate grade input
    if (is_numeric($grade) && $grade >= 0 && $grade <= 100) {
        // Update grade in the database
        $stmt = $conn->prepare("UPDATE submissions SET grade = ? WHERE id = ?");
        $stmt->bind_param("di", $grade, $submission_id);
        $stmt->execute();

        // Redirect back to the course details page
        header("Location: course_details.php?id=" . $_GET['id']);
        exit();
    } else {
        echo "Invalid grade. Please enter a value between 0 and 100.";
    }
}
?>
