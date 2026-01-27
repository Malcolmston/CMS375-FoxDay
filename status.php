<?php

require_once __DIR__ . '/vendor/autoload.php';

use Spatie\Ping\Ping;

$host = 'db';
$result = (new Ping($host))->run();
$latency = $result->averageResponseTimeInMs();
$error = $result->hasError() ? (string) $result->error()->value : null;



header("Content-Type: application/json");
if ($error || !$result->isSuccess()) {
    http_response_code(401);
    echo json_encode(['status' => 'fail', 'latency' => $latency, 'error' => $error]);
    exit;
}

echo json_encode(['status' => 'success', 'latency' => $latency]);
exit;
?>
