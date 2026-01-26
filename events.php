<?php

require_once 'Event.php';

$events = Event::getEvents()
?>


<main>

    <?php foreach($events as $event) : ?>
        <h2><?= $event->getTitle() ?></h2>
        <p><?= $event->getDate() ?></p>
        <p><?= $event->getDescription() ?></p>
    <?php endforeach; ?>

</main>

