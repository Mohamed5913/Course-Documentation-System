<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $assignment_name = $_POST['assignment_name'];
    $assignment_details = $_POST['assignment_details'];
    $due_date = $_POST['due_date'];

    // Insert the assignment into the database
    $stmt = $conn->prepare("INSERT INTO assignments (course_id, assignment_name, assignment_details, due_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $course_id, $assignment_name, $assignment_details, $due_date);
    $stmt->execute();

    echo "Assignment added successfully!";
    header("Location: course_details.php?id=$course_id"); // Redirect to course details page
    exit();
}
?>
