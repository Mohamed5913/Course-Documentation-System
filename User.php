<?php
class User {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function login($username, $password, $role) {
        $table = ($role === 'instructor') ? 'instructors' : 'students';
        $sql = "SELECT id, username, password FROM $table WHERE username = ?";
        $result = $this->db->query($sql, [$username]);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $role;
                return true;
            }
        }
        return false;
    }

    public function register($username, $password, $role) {
        $table = ($role === 'instructor') ? 'instructors' : 'students';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO $table (username, password) VALUES (?, ?)";
        return $this->db->execute($sql, [$username, $hashed_password]);
    }
}
?>