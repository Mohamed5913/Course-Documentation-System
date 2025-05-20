<?php
class Quiz {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    // Fetch quiz details by quiz ID
    public function getQuizDetails($quiz_id) {
        $sql = "SELECT * FROM quizzes WHERE id = ?";
        $result = $this->db->query($sql, [$quiz_id]);
        return $result->fetch_assoc();
    }

    // Fetch quizzes for a course
    public function getQuizzesByCourse($course_id) {
        $sql = "SELECT * FROM quizzes WHERE course_id = ?";
        return $this->db->query($sql, [$course_id]);
    }

    // Fetch questions for a quiz
    public function getQuestions($quiz_id) {
        $sql = "SELECT * FROM questions WHERE quiz_id = ?";
        return $this->db->query($sql, [$quiz_id]);
    }

    // Fetch choices for a question
    public function getChoices($question_id) {
        $sql = "SELECT * FROM choices WHERE question_id = ?";
        return $this->db->query($sql, [$question_id]);
    }

    // Grade the quiz
    public function gradeQuiz($quiz_id, $student_answers) {
        $correct_count = 0;
        $total_questions = 0;

        // Fetch correct answers for the quiz
        $sql = "SELECT question_id, correct_answer FROM correct_answers WHERE quiz_id = ?";
        $result = $this->db->query($sql, [$quiz_id]);

        while ($row = $result->fetch_assoc()) {
            $question_id = $row['question_id'];
            $correct_answer = $row['correct_answer'];

            // Check if the student answered this question
            if (isset($student_answers['question_' . $question_id])) {
                $student_answer = $student_answers['question_' . $question_id];
                if ($student_answer == $correct_answer) {
                    $correct_count++;
                }
            }
            $total_questions++;
        }

        // Calculate score percentage
        $score_percentage = ($total_questions > 0) ? ($correct_count / $total_questions) * 100 : 0;

        return [
            'correct_count' => $correct_count,
            'total_questions' => $total_questions,
            'score_percentage' => round($score_percentage, 2)
        ];
    }
}
?>