<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Course.php';
require_once '../Model/Assignment.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../Controller/login.php");
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
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

        <!-- Courses Tab -->
        <div id="courses-tab" class="tab-content">
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
                            <form action="../Controller/register_course.php" method="POST">
                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                <button type="submit">Register</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-courses-message">No available courses match your search.</p>
            <?php endif; ?>
        </div>

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

            function showTab(tabId) {
                document.getElementById('courses-tab').style.display = (tabId === 'courses-tab') ? 'block' : 'none';
                document.getElementById('notifications-tab').style.display = (tabId === 'notifications-tab') ? 'block' : 'none';
            }
            // Show courses tab by default
            showTab('courses-tab');

            // Notification dropdown toggle
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
    </script>
    
</body>
</html>