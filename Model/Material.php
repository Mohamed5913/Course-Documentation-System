<?php
class Material {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getMaterialsByCourse($course_id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM course_materials WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>