<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);

require_once 'Connect.php';
require_once 'Event.php';
require_once 'User.php';

$events = Event::getEvents();

$name = $email = $event = $year = $message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'student-name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $event = filter_input(INPUT_POST, 'event', FILTER_VALIDATE_INT);
    $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $evt = $event ? Event::initEvent($event) : null;

    if(!$name) {
        $errors[] = 'Please enter your name';
    }
    if(!$email) {
        $errors[] = 'Please enter a valid email';
    }
    if(!$event || !$evt || !$evt->hasEvent()) {
        if($event < 1 || $event > 4) {
            $errors[] = 'Please select a valid event';
        } else {
            $errors[] = 'This event is not available';
        }
    }

    if(!$year) {
        $errors[] = 'Please select a year';
    } elseif($year < 1 || $year > 4) {
        $errors[] = 'Please select a valid year';
    }

    if(!$errors) {
        $user = new User($name, $year, $email);

        if( !$user->createUser() ) {
            $errors[] = $user->getError();
        }

        if (!$errors) {
            $user->addEvent($evt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register • Fox Day</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-display min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 text-slate-100">
<main class="mx-auto max-w-3xl px-6 py-12">
    <?php if ($errors): ?>
        <div class="rounded-2xl border border-red-500/30 bg-red-500/10 px-5 py-4 text-sm text-red-100" role="alert">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-100">
            Registration received. We’ll be in touch soon.
        </div>
    <?php endif; ?>

    <h1 class="mt-10 text-3xl font-semibold text-white">Registration Summary</h1>
    <p class="mt-2 text-sm text-slate-300">Review the details you submitted.</p>

    <div class="mt-6 space-y-3 rounded-2xl border border-white/10 bg-white/5 px-6 py-5 text-sm text-slate-200">
        <p><span class="text-slate-400">Name:</span> <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></p>
        <p><span class="text-slate-400">Email:</span> <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></p>
        <p><span class="text-slate-400">Event:</span> <?php echo htmlspecialchars((string) $event, ENT_QUOTES, 'UTF-8'); ?></p>
        <p><span class="text-slate-400">Year:</span> <?php echo htmlspecialchars((string) $year, ENT_QUOTES, 'UTF-8'); ?></p>
        <p><span class="text-slate-400">Message:</span> <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const alert = document.querySelector('[role="alert"]');

        if(alert) {
            // remove alert after 3 seconds
            setTimeout(() =>  alert.remove(), 3000);
        }

    })
</script>
</body>
</html>
