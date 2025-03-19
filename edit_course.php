<?php
session_start();
include 'db.php'; // Database connection

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['id'];
$instructor_id = $_SESSION['id']; // Get the instructor's ID from session

// Fetch course details
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? AND instructor_id = ?");
$stmt->bind_param("ii", $course_id, $instructor_id); // Using instructor_id for the query
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Course not found or you are not authorized to edit this course.";
    exit();
}

$course = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];

    $update_stmt = $conn->prepare("UPDATE courses SET course_name = ?, description = ? WHERE id = ?");
    $update_stmt->bind_param("ssi", $course_name, $description, $course_id);

    if ($update_stmt->execute()) {
        header("Location: instructor-dashboard.php");
        exit();
    } else {
        echo "Error: " . $update_stmt->error;
    }
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
        <p>&copy; 2024 Course Documentation System. All rights reserved.</p>
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
