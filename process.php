<?php

require_once 'Connect.php';
require_once 'User.php';

$name = $email = $event = $year = $message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $evt = Event::initEvent($_POST['event']);

    // php $_POST sanitization for name, email, event, year, message
    $name = filter_input(INPUT_POST, 'student-name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $event = filter_input(INPUT_POST, 'event', FILTER_VALIDATE_INT);
    $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if(!$name) {
        $errors[] = 'Please enter your name';
    }
    if(!$email) {
        $errors[] = 'Please enter a valid email';
    }
    if(!$event || !$evt->hasEvent()) {
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

<html>
    <body>
        <?php if($errors): ?>
            <ul>
                <?php foreach($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h1>Fox day</h1>
        <p>Register for Fox day</p>

        <span>Name: <?php echo htmlspecialchars($name); ?></span>
    <br>
    <span>Email: <?php echo htmlspecialchars($email); ?></span>
    <br>
    <span>Event: <?php echo htmlspecialchars($event); ?></span>
    <br>
    <span>Year: <?php echo htmlspecialchars($year); ?></span>
    <br>
    <span>Message: <?php echo htmlspecialchars($message); ?></span>


    </body>
</html>
