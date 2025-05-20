<?php
session_start();

include '../Model/Database.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

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
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
