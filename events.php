<?php
require_once 'Event.php';

$events = Event::getEvents()
?>


<main>

    <?php foreach($events as $event) : ?>
        <h2><?= htmlspecialchars($event->getTitle(), ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($event->getDate(), ENT_QUOTES, 'UTF-8') ?></p>
        <p><?= htmlspecialchars($event->getDescription(), ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>

</main>

