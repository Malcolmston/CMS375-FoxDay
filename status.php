<?php

require_once __DIR__ . '/vendor/autoload.php';

use Spatie\Ping\PingDestination;

$mysql = PingDestination::create("127.0.0.1:3306");


if( $mysql->isReachable() ) {
    $latency = $mysql->getLatency();
}

$error = $mysql->getError();



header("Content-Type: application/json");
if ($error) {
    http_response_code(401);
    echo json_encode(['status' => 'fail', 'latency' => $latency, 'error' => $error]);
    exit;
}

echo json_encode(['status' => 'success', 'latency' => $latency]);
exit;
?>
