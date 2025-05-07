<?php
require_once 'Observer.php';
// Handles new course creation events
class CourseCreationObserver implements Observer {
    public function update($eventData) {
        $log = '[' . date('Y-m-d H:i:s') . "] New course created: " . $eventData['course_name'] . " by Instructor ID: " . $eventData['instructor_id'] . "\n";
        file_put_contents('course_creation_log.txt', $log, FILE_APPEND);
    }
}
