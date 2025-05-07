<?php
class Submission {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getSubmissionsByAssignment($assignment_id) {
        $sql = "SELECT * FROM submissions WHERE assignment_id = ?";
        return $this->db->query($sql, [$assignment_id]);
    }

    public function getSubmission($submission_id) {
        $sql = "SELECT * FROM submissions WHERE id = ?";
        $result = $this->db->query($sql, [$submission_id]);
        return $result->fetch_assoc();
    }

    public function gradeSubmission($submission_id, $grade) {
        $sql = "UPDATE submissions SET grade = ? WHERE id = ?";
        return $this->db->execute($sql, [$grade, $submission_id]);
    }
}
?>
