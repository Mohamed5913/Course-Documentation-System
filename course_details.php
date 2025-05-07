<?php
session_start();

require_once 'Database.php';

// Create a new Database instance
$db = new Database();

// Get the database connection
$conn = $db->getConnection();

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['id'];

// Fetch course details
$sql = "SELECT * FROM courses WHERE id = ? AND instructor_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error in query preparation: " . $conn->error);
}
$stmt->bind_param("ii", $course_id, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Course not found or you are not authorized to edit this course.";
    exit();
}

$course = $result->fetch_assoc();

// Fetch assignments for the course
$sql = "SELECT * FROM assignments WHERE course_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error in query preparation: " . $conn->error);
}
$stmt->bind_param("i", $course_id);
$stmt->execute();
$assignments_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - Course Documentation System</title>
    <link rel="stylesheet" href="courses.css">
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
        <h1>Manage Course: <?php echo htmlspecialchars($course['course_name']); ?></h1>
    </header>

    <main>
        <section id="course-info">
            <h3>Description:</h3>
            <p><?php echo htmlspecialchars($course['description']); ?></p>

            <!-- Add Quiz Section -->
            <h3>Quizzes</h3>
            <form action="add_quiz.php" method="GET">
                <label for="quiz_title">Add Quiz Title:</label>
                <input type="text" id="quiz_title" name="quiz_title" placeholder="Enter quiz title" required>

                <!-- Pass course_id as a GET parameter -->
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                <div class="form-buttons">
                    <button type="submit">Add Quiz</button>
                </div>
            </form>

            <!-- Materials and Assignments -->
            <h3>Materials</h3>
            <!-- Material upload form -->
            <form action="upload_material.php" method="POST" enctype="multipart/form-data">
                <label for="material_title">Material Title:</label>
                <input type="text" id="material_title" name="material_title" required>
                
                <label for="material">Upload Material (e.g., PowerPoint slides):</label>
                <input type="file" name="material" required>
                
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                <div class="form-buttons">
                    <button type="submit">Upload Material</button>
                </div>
            </form>

            <!-- Assignments Section -->
            <h3>Assignments</h3>
            <form action="upload_material.php" method="POST" enctype="multipart/form-data">
                <label for="assignment_title">Assignment Title:</label>
                <input type="text" id="assignment_title" name="assignment_title" placeholder="Enter assignment title" required>

                <label for="assignment_details">Assignment Description:</label>
                <input type="text" id="assignment_details" name="assignment_details" placeholder="Enter assignment title" required>

                <label for="due_date">Due Date:</label>
                <input type="datetime-local" name="due_date" id="due_date" required>

                <label for="assignment_file">Upload Assignment (PDF/Word):</label>
                <input type="file" name="assignment_file" accept=".pdf, .doc, .docx" required>

                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                <div class="form-buttons">
                    <button type="submit" name="upload_assignment">Upload Assignment</button>
                </div>
            </form>

            <!-- "Check Submissions" Button -->
            <form action="submissions.php" method="GET">
                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                <button type="submit">Check Submissions</button>
            </form>
        </section>
    </main>

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