<?php
require_once '../View/Observer.php';
// Logs events to a file
class LoggingObserver implements Observer {
    public function update($eventData) {
        $log = '[' . date('Y-m-d H:i:s') . "] " . $eventData['message'] . "\n";
        file_put_contents('event_log.txt', $log, FILE_APPEND);
    }
}
