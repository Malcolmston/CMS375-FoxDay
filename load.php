<?php
header('Content-Type: application/json');

require_once 'Event.php';

$events = Event::getEvents();

$response = array_map(function($event) {
    return [
        'id' => $event->getId(),
        'title' => $event->getTitle(),
        'date' => $event->getDate(),
        'description' => $event->getDescription()
    ];
}, $events);

echo json_encode($response);
