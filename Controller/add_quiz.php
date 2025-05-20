<?php
session_start();

require_once '../Model/Database.php';
require_once '../Model/Quiz.php';

$db = new Database();
$quiz = new Quiz($db);

// Ensure the user is logged in and is an instructor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['course_id']; // Get the course ID from the query parameter

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_title = $_POST['quiz_title'];
    $questions = $_POST['questions'];
    $answers = $_POST['answers'];

    // Insert the quiz into the database
    $sql = "INSERT INTO quizzes (course_id, title) VALUES (?, ?)";
    $stmt = $db->getConnection()->prepare($sql);
    if ($stmt === false) {
        die("Error in query preparation: " . $db->getConnection()->error);
    }
    $stmt->bind_param("is", $course_id, $quiz_title);
    $stmt->execute();
    $quiz_id = $stmt->insert_id;

    // Insert questions and choices into the database
    foreach ($questions as $index => $question) {
        $sql = "INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)";
        $stmt = $db->getConnection()->prepare($sql);
        if ($stmt === false) {
            die("Error in query preparation: " . $db->getConnection()->error);
        }
        $stmt->bind_param("is", $quiz_id, $question);
        $stmt->execute();
        $question_id = $stmt->insert_id;

        // Insert choices
        for ($i = 0; $i < 4; $i++) {
            $choice_text = $_POST['choices'][$index][$i];
            $sql = "INSERT INTO choices (question_id, choice_text) VALUES (?, ?)";
            $stmt = $db->getConnection()->prepare($sql);
            if ($stmt === false) {
                die("Error in query preparation: " . $db->getConnection()->error);
            }
            $stmt->bind_param("is", $question_id, $choice_text);
            $stmt->execute();
        }

        // Insert the correct answer
        $correct_answer = intval($answers[$index]);
        $sql = "INSERT INTO correct_answers (question_id, correct_answer, quiz_id) VALUES (?, ?, ?)";
        $stmt = $db->getConnection()->prepare($sql);
        if ($stmt === false) {
            die("Error in query preparation: " . $db->getConnection()->error);
        }
        $stmt->bind_param("iii", $question_id, $correct_answer, $quiz_id);
        $stmt->execute();
    }

    header("Location: ../View/instructor_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Quiz</title>
    <link rel="stylesheet" href="../View/add_quiz.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0/css/all.min.css" />
    <script>
        let questionCount = 1;

        function addQuestion() {
            const container = document.getElementById('questions-container');

            const questionDiv = document.createElement('div');
            questionDiv.className = 'question';

            // Increment the question number and update the label
            questionCount++;

            questionDiv.innerHTML = `
                <label>Question ${questionCount}:</label>
                <textarea name="questions[]" class="question-textbox" required></textarea>
                <div class="choices">
                    <label>Choice A:</label>
                    <input type="text" name="choices[${questionCount - 1}][]" required>
                    <label>Choice B:</label>
                    <input type="text" name="choices[${questionCount - 1}][]" required>
                    <label>Choice C:</label>
                    <input type="text" name="choices[${questionCount - 1}][]" required>
                    <label>Choice D:</label>
                    <input type="text" name="choices[${questionCount - 1}][]" required>
                </div>
                <label>Correct Answer (e.g., 1, 2, 3, 4):</label>
                <input type="text" name="answers[]" required>
                <hr>
            `;
            container.appendChild(questionDiv);
        }
    </script>
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
        <h1>Add Quiz for Course</h1>
    </header>
    <main>
        <form action="add_quiz.php?course_id=<?php echo $course_id; ?>" method="POST">
            <label for="quiz_title">Quiz Title:</label>
            <input type="text" id="quiz_title" name="quiz_title" required>
            
            <div id="questions-container">
                <div class="question">
                    <label>Question 1:</label>
                    <textarea name="questions[]" required></textarea>
                    <div class="choices">
                        <label>Choice A:</label>
                        <input type="text" name="choices[0][]" required>
                        <label>Choice B:</label>
                        <input type="text" name="choices[0][]" required>
                        <label>Choice C:</label>
                        <input type="text" name="choices[0][]" required>
                        <label>Choice D:</label>
                        <input type="text" name="choices[0][]" required>
                    </div>
                    <label>Correct Answer (e.g., 1, 2, 3, 4):</label>
                    <input type="text" name="answers[]" required>
                    <hr>
                </div>
            </div>
            <button type="button" onclick="addQuestion()">Add Another Question</button>
            <button type="submit">Save Quiz</button>
        </form>
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