<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Course.php';

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../Controller/login.php");
    exit();
}

$course_id = $_GET['id'];
$instructor_id = $_SESSION['id'];

$db = new Database();
$courseModel = new Course($db);

// Fetch course details
$course = $courseModel->getCourseDetails($course_id);
if (!$course || $course['instructor_id'] != $instructor_id) {
    echo "Course not found or you are not authorized to edit this course.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $sql = "UPDATE courses SET course_name = ?, description = ? WHERE id = ?";
    $db->execute($sql, [$course_name, $description, $course_id]);
    header("Location: instructor-dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="dashboardstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0/css/all.min.css" />
</head>
<body>
    <header>
        <h1>Edit Course</h1>
    </header>

    <!-- Night Mode Toggle -->
    <div class="night-mode-button">
        <input type="checkbox" class="checkbox" id="night-mode">
        <label for="night-mode" class="label">
            <i class="fas fa-moon" id="moon-icon"></i>
            <i class="fas fa-sun" id="sun-icon"></i>
            <div class="blob"></div>
        </label>
    </div>

    <main>
        <form action="edit_course.php?id=<?php echo $course_id; ?>" method="POST">
            <label for="course_name">Course Name:</label>
            <input type="text" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($course['description']); ?></textarea>
            
            <button type="submit">Save Changes</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 Course Documentation System. All rights reserved.</p>
    </footer>

    <script>
        // Night Mode Toggle Functionality
        const checkbox = document.getElementById("night-mode");
        const body = document.body;

        // Check local storage for night mode preference
        if (localStorage.getItem('night-mode') === 'enabled') {
            body.classList.add("night");
            checkbox.checked = true;
        }

        // Toggle night mode
        checkbox.addEventListener("change", function() {
            if (this.checked) {
                body.classList.add("night");
                localStorage.setItem('night-mode', 'enabled');
            } else {
                body.classList.remove("night");
                localStorage.setItem('night-mode', 'disabled');
            }
        });
    </script>
</body>
</html>
