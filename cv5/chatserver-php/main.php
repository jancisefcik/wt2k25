<?php
// DOCUMENTATION: https://manual.workerman.net/doc/en/

use Workerman\Worker;
use Workerman\Connection\TcpConnection;

require_once __DIR__ . '/vendor/autoload.php';

$ws_worker = new Worker("websocket://127.0.0.1:8081");
$ws_worker->count = 1; // 1 proces



$ws_worker->onConnect = function($connection) {
    echo("New connection established\n");
};

$ws_worker->onMessage = function(TcpConnection $connection, $data) use ($ws_worker)
{   
    echo "Received message: $data\n";
    foreach ($ws_worker->connections as $conn) {
        $conn->send($data);
    }
};

$ws_worker->onClose = function(TcpConnection $connection)
{
    echo "connection closed\n";
};



// Run the worker
Worker::runAll();
