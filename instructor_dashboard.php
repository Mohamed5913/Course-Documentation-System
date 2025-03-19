<?php
session_start();
require_once 'Database.php';
require_once 'Course.php';
require_once 'Assignment.php';

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$course = new Course($db);
$assignment = new Assignment($db);

// Fetch courses created by the logged-in instructor
$instructor_id = $_SESSION['id'];
$courses = $course->getCoursesByInstructor($instructor_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="instructor_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0/css/all.min.css" />
</head>
<body>
    <!-- Night Mode Toggle -->
    <div class="night-mode-button">
        <input type="checkbox" class="checkbox" id="night-mode">
        <label for="night-mode" class="label">
            <i class="fas fa-moon" id="moon-icon"></i>
            <i class="fas fa-sun" id="sun-icon"></i>
            <div class="blob"></div>
        </label>
    </div>

    <header>
        <h1>Instructor Dashboard</h1>
        <a href="logout.php" class="logout">Logout</a>
    </header>

    <main>
        <section id="your-courses">
            <h2>Your Courses</h2>
            <?php if ($courses->num_rows > 0): ?>
                <ul>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <li>
                            <a href="course_details.php?id=<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></a>
                            <p><?= htmlspecialchars($course['description']) ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No courses found. Add your first course!</p>
            <?php endif; ?>
        </section>

        <!-- Add New Course Section -->
        <section id="add-course">
            <h2>Add a New Course</h2>
            <form action="add_course.php" method="POST">
                <div class="form-group">
                    <label for="course-name">Course Name:</label>
                    <input type="text" id="course-name" name="course_name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="course_instructor">Instructor:</label>
                    <input type="text" id="course_instructor" name="course_instructor" value="<?= $_SESSION['username'] ?>" readonly required>
                </div>
                <button type="submit">Add Course</button>
            </form>
        </section>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkbox = document.getElementById("night-mode");
            const body = document.body;

            // Check local storage for night mode preference
            if (localStorage.getItem("night-mode") === "enabled") {
                body.classList.add("night");
                checkbox.checked = true;
            }

            // Toggle night mode and save preference
            checkbox.addEventListener("change", function () {
                if (checkbox.checked) {
                    body.classList.add("night");
                    localStorage.setItem("night-mode", "enabled");
                } else {
                    body.classList.remove("night");
                    localStorage.setItem("night-mode", "disabled");
                }
            });
        });
    </script>
</body>
</html>