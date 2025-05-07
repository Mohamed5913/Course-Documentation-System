<?php
// FileUploadInterface defines the contract for file uploads
interface FileUploadInterface {
    public function uploadFile($file, $target_dir = "");
}

// Base FileUpload class
class FileUpload implements FileUploadInterface {
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

// Abstract Decorator
abstract class FileUploadDecorator implements FileUploadInterface {
    protected $fileUpload;

    public function __construct(FileUploadInterface $fileUpload) {
        $this->fileUpload = $fileUpload;
    }

    public function uploadFile($file, $target_dir = "") {
        return $this->fileUpload->uploadFile($file, $target_dir);
    }
}

// Concrete Decorator: Logging
class LoggingFileUpload extends FileUploadDecorator {
    public function uploadFile($file, $target_dir = "") {
        $result = parent::uploadFile($file, $target_dir);
        $logMessage = date('Y-m-d H:i:s') . " - File: " . $file['name'] . " - Result: " . ($result ? 'Success' : 'Failure') . "\n";
        file_put_contents('upload_log.txt', $logMessage, FILE_APPEND);
        return $result;
    }
}
?>