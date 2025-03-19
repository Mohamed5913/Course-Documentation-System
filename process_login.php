<?php
session_start();
require_once 'Database.php';
require_once 'User.php';

$db = new Database();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($user->login($username, $password, $role)) {
        header("Location: " . ($role === 'instructor' ? 'instructor_dashboard.php' : 'student_dashboard.php'));
        exit();
    } else {
        echo "Invalid credentials!";
    }
}
?>