<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Quiz.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$quiz = new Quiz($db);

$quiz_id = $_POST['quiz_id'];
$course_id = $_POST['course_id'];

// Grade the quiz
$result = $quiz->gradeQuiz($quiz_id, $_POST);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="../View/take_quiz.css">
</head>
<body>
    <header>
        <h1>Quiz Results</h1>
    </header>

    <main>
        <div class="quiz-result">
            <h2>Your Results</h2>
            <p>You got <?= $result['correct_count'] ?> correct answers out of <?= $result['total_questions'] ?> questions.</p>
            <p>Your score: <?= $result['score_percentage'] ?>%</p>
            <?php if ($result['score_percentage'] > 50): ?>
                <p class="pass">Congratulations! You passed the quiz.</p>
            <?php else: ?>
                <p class="fail">Unfortunately, you did not pass the quiz. Better luck next time!</p>
            <?php endif; ?>
        </div>

        <!-- Redirection Button -->
        <button onclick="window.location.href='../View/view_course.php?id=<?= $course_id ?>'" class="redirect-btn">
            Go Back to Course
        </button>
    </main>
</body>
</html>