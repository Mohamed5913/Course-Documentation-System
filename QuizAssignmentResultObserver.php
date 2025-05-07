<?php
require_once 'Observer.php';
require_once 'Database.php';
// Handles quiz/assignment result events
class QuizAssignmentResultObserver implements Observer {
    public function update($eventData) {
        $db = new Database();
        $conn = $db->getConnection();

        // Example: Notify student about their grade
        $student_id = $eventData['student_id'] ?? null;
        $message = $eventData['message'] ?? '';

        if ($student_id && $message) {
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, user_role, message) VALUES (?, 'student', ?)");
            $stmt->bind_param("is", $student_id, $message);
            $stmt->execute();
        }
    }
}
