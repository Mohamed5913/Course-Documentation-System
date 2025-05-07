<?php
require_once 'Observer.php';
// Handles material upload events
class MaterialUploadObserver implements Observer {
    public function update($eventData) {
        // Example: send notification or perform other actions
        // For now, just log to a separate file
        $log = '[' . date('Y-m-d H:i:s') . "] Material uploaded: " . $eventData['material_title'] . " by Instructor ID: " . $eventData['instructor_id'] . "\n";
        file_put_contents('material_upload_log.txt', $log, FILE_APPEND);
    }
}
