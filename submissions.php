<?php
session_start();
include 'db.php';  // Ensure you're connected to the database

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$assignment_id = $_GET['assignment_id'];

// Fetch the assignment details
$assignment_stmt = $conn->prepare("SELECT * FROM assignments WHERE id = ?");
$assignment_stmt->bind_param("i", $assignment_id);
$assignment_stmt->execute();
$assignment_result = $assignment_stmt->get_result();

if ($assignment_result->num_rows === 0) {
    echo "Assignment not found.";
    exit();
}

$assignment = $assignment_result->fetch_assoc();


// Fetch student submissions for the assignment
$submissions_stmt = $conn->prepare("SELECT * FROM submissions WHERE assignment_id = ?");
$submissions_stmt->bind_param("i", $assignment_id);
$submissions_stmt->execute();
$submissions_result = $submissions_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submissions - Course Documentation System</title>
    <link rel="stylesheet" href="courses.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0/css/all.min.css" />
</head>
<body>
    <header>
        <h1>Submissions for: <?php echo htmlspecialchars($assignment['title']); ?></h1>
    </header>

    <main>
        <section id="submissions">
            <h3>Student Submissions</h3>
            <?php while ($submission = $submissions_result->fetch_assoc()): ?>
                <div class="submission">
                    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($submission['student_id']); ?></p>
                    <p><strong>File:</strong> <a href="<?php echo htmlspecialchars($submission['file_path']); ?>"><?php echo htmlspecialchars($submission['file_name']); ?></a></p>

                    <!-- Grade input form for instructor -->
                    <form action="grade_submission.php" method="POST">
                        <label for="grade_<?php echo $submission['id']; ?>">Grade:</label>
                        <input type="number" name="grade" id="grade_<?php echo $submission['id']; ?>" min="0" max="100" value="<?php echo htmlspecialchars($submission['grade']); ?>" required>

                        <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                        <button type="submit">Submit Grade</button>
                    </form>

                    <!-- Display grade if it exists -->
                    <?php if ($submission['grade'] !== NULL): ?>
                        <p><strong>Grade:</strong> <?php echo htmlspecialchars($submission['grade']); ?></p>
                    <?php else: ?>
                        <p>No grade assigned yet.</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
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
