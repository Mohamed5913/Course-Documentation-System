<?php
session_start();
require_once '../Model/Database.php';
require_once '../Model/Quiz.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../Controller/login.php");
    exit();
}

$db = new Database();
$quiz = new Quiz($db);

$quiz_id = $_GET['quiz_id'];
$course_id = $_GET['course_id'];

// Fetch quiz details
$quiz_details = $quiz->getQuizDetails($quiz_id);

// Fetch questions for the quiz
$questions = $quiz->getQuestions($quiz_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - <?= htmlspecialchars($quiz_details['title']) ?></title>
    <link rel="stylesheet" href="take_quiz.css">
</head>
<body>
    <header>
        <h1>Take Quiz: <?= htmlspecialchars($quiz_details['title']) ?></h1>
    </header>

    <main>
        <form action="../Controller/submit_quiz.php" method="POST">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">
            
            <!-- Display each question with choices -->
            <?php while ($question = $questions->fetch_assoc()): ?>
                <div class="question">
                    <p><?= htmlspecialchars($question['question_text']) ?></p>
                    <?php
                    // Fetch choices for this question
                    $choices = $quiz->getChoices($question['id']);
                    $counter = 1;
                    while ($choice = $choices->fetch_assoc()):
                    ?>
                        <label>
                            <input type="radio" name="question_<?= $question['id'] ?>" value="<?= $counter ?>">
                            <?= htmlspecialchars($choice['choice_text']) ?>
                        </label><br>
                    <?php
                        $counter++;
                    endwhile;
                    ?>
                </div>
            <?php endwhile; ?>

            <button type="submit">Submit Quiz</button>
        </form>
    </main>
</body>
</html>