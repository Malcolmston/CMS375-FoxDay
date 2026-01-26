<?php
require_once 'Event.php';

$events = Event::getEvents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events • Fox Day</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-display min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 text-slate-100">
<main class="mx-auto max-w-6xl px-6 py-12">
    <h1 class="text-3xl font-semibold text-white">Upcoming Events</h1>
    <p class="mt-2 text-sm text-slate-300">Browse upcoming Fox Day events and details.</p>

    <?php if (empty($events)) : ?>
        <p class="mt-8 text-sm text-slate-400">No events available yet.</p>
    <?php else : ?>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($events as $event) : ?>
                <article class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl shadow-blue-500/10">
                    <h2 class="text-xl font-semibold text-white">
                        <?= htmlspecialchars($event->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                    <p class="mt-3 text-sm text-slate-300">
                        <?= htmlspecialchars($event->getDescription(), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <span class="mt-4 inline-flex items-center rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                        <?= htmlspecialchars($event->getDate(), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
