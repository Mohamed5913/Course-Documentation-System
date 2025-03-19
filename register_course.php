<?php
session_start();

// Include the Database class
require_once 'Database.php';

// Create a new Database instance
$db = new Database();

// Get the database connection
$conn = $db->getConnection();

// Fetch available courses for registration
$query = "SELECT * FROM courses";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle course registration
    $course_id = $_POST['course_id'];
    $student_id = $_SESSION['id'];

    // Check if the student is already registered for the selected course
    $check_query = "SELECT * FROM course_registrations WHERE student_id = ? AND course_id = ?";
    $stmt_check = $conn->prepare($check_query);
    if ($stmt_check === false) {
        die("Error in query preparation: " . $conn->error);
    }
    $stmt_check->bind_param("ii", $student_id, $course_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // If the student is already registered, display an error message
        echo "<div class='error-message'>You are already registered for this course.</div>";
    } else {
        // If the student is not registered, proceed with the registration
        $stmt = $conn->prepare("INSERT INTO course_registrations (student_id, course_id) VALUES (?, ?)");
        if ($stmt === false) {
            die("Error in query preparation: " . $conn->error);
        }
        $stmt->bind_param("ii", $student_id, $course_id);
        
        if ($stmt->execute()) {
            // Redirect the student to the courses page after successful registration
            header("Location: student_dashboard.php");  // Redirect to the student dashboard or courses page
            exit();
        } else {
            echo "<div class='error-message'>Error: " . $stmt->error . "</div>";
        }
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