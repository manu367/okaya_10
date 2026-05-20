<?php
require '../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MyWebSocket implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New client connected\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Received: $msg\n";

        // Example JSON to send back
        $data = [
            "status" => "ok",
            "message" => "I am manu Pathak".date('Y-m-d'),
            "time" => date("H:i:s")
        ];

        $json = json_encode($data);

        // Send to all connected clients
        foreach ($this->clients as $client) {
            $client->send($json);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Client disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = Ratchet\Server\IoServer::factory(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(new MyWebSocket())
    ),
    8080
);

echo "WebSocket server running on ws://localhost:8080\n";
$server->run();
