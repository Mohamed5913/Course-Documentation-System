<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Course.php';
require_once '../Model/Assignment.php';

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../Controller/login.php");
    exit();
}

$db = new Database();
$course = new Course($db);
$assignment = new Assignment($db);

// Fetch courses created by the logged-in instructor
$instructor_id = $_SESSION['id'];
$courses = $course->getCoursesByInstructor($instructor_id);

// Fetch notifications for the student
$notifications = [];
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT id, message, is_read FROM notifications WHERE user_id = ? AND user_role = 'student' ORDER BY created_at DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$unread = array_filter($notifications, fn($n) => !$n['is_read']);

// Handle marking notifications as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read_id'])) {
    $notif_id = intval($_POST['mark_read_id']);
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notif_id, $student_id);
    $stmt->execute();
    // Refresh to update the notifications list
    echo "<script>window.location.href=window.location.href;</script>";
    exit();
}
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
        <h1 class="dashboard-title">Instructor Dashboard</h1>
        <div class="header-actions">
            <!-- Notification Bell -->
            <div class="notification-bell-container">
                <button id="notification-bell" class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <?php if (count($unread) > 0): ?>
                        <span class="notification-count"><?= count($unread) ?></span>
                    <?php endif; ?>
                </button>
                <div id="notifications-dropdown" class="notifications-dropdown" style="display:none;">
                    <h4>Notifications</h4>
                    <?php if (count($notifications) > 0): ?>
                        <ul class="notifications-list">
                            <?php foreach ($notifications as $notif): ?>
                                <li style="<?php if(!$notif['is_read']) echo 'font-weight:bold;'; ?>">
                                    <?= htmlspecialchars($notif['message']) ?>
                                    <?php if (!$notif['is_read']): ?>
                                        <form method="POST" action="" style="display:inline;">
                                            <input type="hidden" name="mark_read_id" value="<?= $notif['id'] ?>">
                                            <button type="submit" class="mark-read-btn">Mark as read</button>
                                        </form>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-courses-message">No notifications.</p>
                    <?php endif; ?>
                </div>
            </div>
            <a href="../Controller/logout.php" class="logout">Logout</a>
        </div>
    </header>

    <main>
        <section id="your-courses">
            <h2>Your Courses</h2>
            <?php if ($courses->num_rows > 0): ?>
                <ul>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <li>
                            <div class="course-info" id="course-info-<?= $course['id'] ?>">
                                <span class="course-title"><?= htmlspecialchars($course['course_name']) ?></span>
                                <p><?= htmlspecialchars($course['description']) ?></p>
                                <button class="edit-btn" onclick="showEditForm(<?= $course['id'] ?>)">Edit</button>
                                <form class="delete-course-form" action="../Controller/delete_course.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this course?');" style="color:red;">Delete</button>
                                </form>
                            </div>
                            <form class="edit-course-form" id="edit-form-<?= $course['id'] ?>" action="../Controller/edit_course.php" method="POST" style="display:none;">
                                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                <input type="text" name="course_name" value="<?= htmlspecialchars($course['course_name']) ?>" required>
                                <textarea name="description" required><?= htmlspecialchars($course['description']) ?></textarea>
                                <button type="submit">Save</button>
                                <button type="button" onclick="hideEditForm(<?= $course['id'] ?>)">Cancel</button>
                            </form>
                            <a href="course_details.php?id=<?= $course['id'] ?>" class="access-course-btn">Access Course</a>
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
            <form action="../Controller/add_course.php" method="POST">
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

        <!-- Notifications Tab -->
        <div id="notifications-tab" class="tab-content" style="display:none;">
            <h3>Your Notifications</h3>
            <?php if (count($notifications) > 0): ?>
                <ul class="notifications-list">
                    <?php foreach ($notifications as $notif): ?>
                        <li style="<?php if(!$notif['is_read']) echo 'font-weight:bold;'; ?>">
                            <?= htmlspecialchars($notif['message']) ?>
                            <small>(<?= $notif['created_at'] ?>)</small>
                            <?php if (!$notif['is_read']): ?>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="mark_read_id" value="<?= $notif['id'] ?>">
                                    <button type="submit" class="mark-read-btn">Mark as read</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No notifications.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <script>
        window.onload = function() {
            alert("Course has been created successfully!");
        }
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['edit_success'])): ?>
    <script>
        alert('Course updated successfully!');
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['delete_success'])): ?>
    <script>
        alert('Course deleted successfully!');
    </script>
    <?php endif; ?>

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

            const bell = document.getElementById('notification-bell');
            const dropdown = document.getElementById('notifications-dropdown');
            document.addEventListener('click', function(event) {
                // If bell is clicked, toggle dropdown
                if (bell.contains(event.target)) {
                    dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
                } else {
                    // Hide dropdown if clicked outside
                    dropdown.style.display = 'none';
                }
            });
        });

        // Show edit form for a course
        function showEditForm(courseId) {
            document.getElementById('course-info-' + courseId).style.display = 'none';
            document.getElementById('edit-form-' + courseId).style.display = 'block';
        }

        // Hide edit form for a course
        function hideEditForm(courseId) {
            document.getElementById('course-info-' + courseId).style.display = 'block';
            document.getElementById('edit-form-' + courseId).style.display = 'none';
        }
    </script>
</body>
</html>