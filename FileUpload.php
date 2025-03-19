<?php
// FileUpload.php
class FileUpload {
    private $upload_dir = "uploads/";

    public function uploadFile($file, $target_dir = "") {
        if ($target_dir === "") {
            $target_dir = $this->upload_dir;
        }
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        }
        return false;
    }
}
?>