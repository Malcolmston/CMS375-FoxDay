<?php

/*
o title
o date/time
o location
o description
 */


$EVENTS = [
        ['id' => 1, 'title' => 'Event 1', 'date' => '2023-10-01', 'description' => 'Description for Event 1'],
        ['id' => 2, 'title' => 'Event 2', 'date' => '2023-10-02', 'description' => 'Description for Event 2'],
        ['id' => 3, 'title' => 'Event 3', 'date' => '2023-10-03', 'description' => 'Description for Event 3'],
        ['id' => 4, 'title' => 'Event 4', 'date' => '2023-10-04', 'description' => 'Description for Event 4'],
];
?>


<main>

    <?php foreach($EVENTS as $event) : ?>
        <h2><?= $event['title'] ?></h2>
        <p><?= $event['date'] ?></p>
        <p><?= $event['description'] ?></p>
    <?php endforeach; ?>

</main>

