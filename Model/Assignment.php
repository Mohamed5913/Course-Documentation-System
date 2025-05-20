<?php
class Assignment {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function addAssignment($course_id, $assignment_title, $assignment_details, $due_date, $file_path = null) {
        $sql = "INSERT INTO assignments (course_id, assignment_title, assignment_details, due_date, assignment_file) VALUES (?, ?, ?, ?, ?)";
        return $this->db->execute($sql, [$course_id, $assignment_title, $assignment_details, $due_date, $file_path]);
    }

    public function getAssignmentsByCourse($course_id) {
        $sql = "SELECT * FROM assignments WHERE course_id = ?";
        return $this->db->query($sql, [$course_id]);
    }

    public function submitAssignment($assignment_id, $student_id, $file_path, $file_name) {
        $sql = "INSERT INTO submissions (assignment_id, student_id, file_path, file_name) VALUES (?, ?, ?, ?)";
        return $this->db->execute($sql, [$assignment_id, $student_id, $file_path, $file_name]);
    }
}
?>