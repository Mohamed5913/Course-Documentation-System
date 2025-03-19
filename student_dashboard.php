<?php
session_start();
require_once 'Database.php';
require_once 'Course.php';
require_once 'Assignment.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$course = new Course($db);
$assignment = new Assignment($db);

// Fetch the student's ID from the session
$username = $_SESSION['username'];
$student_id = $_SESSION['id'];

// Handle search query
$search_query = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";

// Fetch the student's registered courses based on the search query
$registered_courses = $course->getCoursesByStudent($student_id, $search_query);

// Fetch all available courses based on the search query (excluding already registered ones)
$available_courses = $course->getAvailableCourses($student_id, $search_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="student_dashboard.css">
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
        <h1>Student Dashboard</h1>
        <a href="logout.php" class="logout">Logout</a>
    </header>

    <main>
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

        <!-- Search Bar -->
        <form method="GET" action="student_dashboard.php" class="search-bar">
            <input type="text" name="search" placeholder="Search for a course..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Search</button>
        </form>

        <h3>Your Registered Courses</h3>
        <?php if ($registered_courses->num_rows > 0): ?>
            <div class="course-list">
                <?php while ($course = $registered_courses->fetch_assoc()): ?>
                    <div class="course-card">
                        <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($course['description']); ?></p>
                        <button onclick="window.location.href='view_course.php?id=<?php echo $course['id']; ?>'">View Course</button>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-courses-message">No registered courses match your search.</p>
        <?php endif; ?>

        <h3>Available Courses to Register</h3>
        <?php if ($available_courses->num_rows > 0): ?>
            <div class="course-list">
                <?php while ($course = $available_courses->fetch_assoc()): ?>
                    <div class="course-card">
                        <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($course['description']); ?></p>
                        <form action="register_course.php" method="POST">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <button type="submit">Register</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-courses-message">No available courses match your search.</p>
        <?php endif; ?>
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