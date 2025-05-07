<?php
session_start();
require_once 'Database.php';
require_once 'Course.php';

$db = new Database();
$course = new Course($db);

$conn = $db->getConnection();
$query = "SELECT * FROM courses";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $student_id = $_SESSION['id'];

    // Use OOP method for registration
    if ($course->registerStudentToCourse($student_id, $course_id)) {
        header("Location: student_dashboard.php");
        exit();
    } else {
        echo "<div class='error-message'>You are already registered for this course or an error occurred.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for a Course</title>
</head>
<body>
    <div class="container">
        <h1 class="page-title">Register for a Course</h1>
        <div class="course-selection">
            <h2>Select a Course</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="course_id">Choose a Course:</label>
                    <select name="course_id" id="course_id">
                        <?php while ($course = $result->fetch_assoc()): ?>
                            <option value="<?= $course['id']; ?>"><?= htmlspecialchars($course['course_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
        </div>
    </div>
</body>
</html>