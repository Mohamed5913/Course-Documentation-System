<?php
// Subject base class for the Observer pattern
class Subject {
    private $observers = [];

    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer) {
        foreach ($this->observers as $key => $obs) {
            if ($obs === $observer) {
                unset($this->observers[$key]);
            }
        }
    }

    public function notify($eventData) {
        foreach ($this->observers as $observer) {
            $observer->update($eventData);
        }
    }
}
