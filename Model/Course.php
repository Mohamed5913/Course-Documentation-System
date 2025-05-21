<?php
class Course {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function addCourse($course_name, $description, $instructor_id) {
        $sql = "INSERT INTO courses (course_name, description, instructor_id) VALUES (?, ?, ?)";
        return $this->db->execute($sql, [$course_name, $description, $instructor_id]);
    }

    public function getCoursesByInstructor($instructor_id) {
        $sql = "SELECT * FROM courses WHERE instructor_id = ?";
        return $this->db->query($sql, [$instructor_id]);
    }

    public function getCourseDetails($course_id) {
        $sql = "SELECT * FROM courses WHERE id = ?";
        return $this->db->query($sql, [$course_id])->fetch_assoc();
    }

    public function getCoursesByStudent($student_id, $search_query = "%") {
        $sql = "
            SELECT c.id, c.course_name, c.description 
            FROM course_registrations cr
            JOIN courses c ON cr.course_id = c.id
            WHERE cr.student_id = ? AND c.course_name LIKE ?";
        return $this->db->query($sql, [$student_id, $search_query]);
    }

    public function getAvailableCourses($student_id, $search_query = "%") {
        $sql = "
            SELECT id, course_name, description 
            FROM courses 
            WHERE id NOT IN (
                SELECT course_id FROM course_registrations WHERE student_id = ?
            ) AND course_name LIKE ?";
        return $this->db->query($sql, [$student_id, $search_query]);
    }

    public function registerStudentToCourse($student_id, $course_id) {
        // Check if already registered
        $sql = "SELECT * FROM course_registrations WHERE student_id = ? AND course_id = ?";
        $result = $this->db->query($sql, [$student_id, $course_id]);
        if ($result->num_rows > 0) {
            return false; // Already registered
        }
        // Register the student
        $sql = "INSERT INTO course_registrations (student_id, course_id) VALUES (?, ?)";
        return $this->db->execute($sql, [$student_id, $course_id]);
    }

    public function updateCourse($course_id, $course_name, $description) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE courses SET course_name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $course_name, $description, $course_id);
        return $stmt->execute();
    }

    public function deleteCourse($course_id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $course_id);
        return $stmt->execute();
    }
}
?>