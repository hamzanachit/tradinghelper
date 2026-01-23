<?php

// Simple WebSocket server for testing
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Services\BinanceWebSocketServer;

require __DIR__ . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new BinanceWebSocketServer()
        )
    ),
    8080
);

$server->run();
