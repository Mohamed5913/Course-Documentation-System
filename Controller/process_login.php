<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/User.php';

$db = new Database();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($user->login($username, $password, $role)) {
        header("Location: " . ($role === 'instructor' ? '../View/instructor_dashboard.php' : '../View/student_dashboard.php'));
        exit();
    } else {
        echo "Invalid credentials!";
    }
}
?>