<?php
session_start();
include 'db.php';  // Database connection

// Check the role of the user (instructor or student)
if ($_SESSION['role'] === 'instructor') {
    // Fetch courses added by the instructor
    $query = "SELECT * FROM courses WHERE instructor_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
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
    // Fetch courses the student is registered for
    $query = "SELECT c.course_name, c.description 
              FROM courses c
              JOIN course_registrations r ON c.id = r.course_id
              WHERE r.student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
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
