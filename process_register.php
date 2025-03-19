<?php
session_start();

// Include the database configuration file
include 'db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Role can be 'student' or 'instructor'

    // Validate role
    if (!in_array($role, ['student', 'instructor'])) {
        echo "Invalid role selected.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepare the statement for inserting data
        $stmt = $conn->prepare("INSERT INTO {$role}s (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Registration successful!";
            header("Location: login.php"); // Redirect to the login page
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
