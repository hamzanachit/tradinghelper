<?php

namespace App\Services;

use React\EventLoop\Loop;
use React\Socket\Server;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\Ws\Support\FacadesServer;
use Illuminate\Cache;

class BinanceWebSocketServer
{
    protected $loop;
    protected $wsServer;
    protected $connections = [];
    protected $binanceWs;
    protected $reconnectAttempts = 0;
    protected $maxReconnectAttempts = 10;

    public function __construct()
    {
        $this->loop = Loop::get();
    }

    public function start()
    {
        $port = 3001;
        
        $socket = new Server('0.0.0.0:' . $port, $this->loop);
        
        $this->wsServer = new IoServer(
            new HttpServer(
                new WsServer($this)
            ),
            $socket
        );

        $this->wsServer->run();
    }

    public function onOpen($conn)
    {
        $this->connections[$conn->resourceId] = $conn;
        echo "Client connected: {$conn->resourceId}\n";
    }

    public function onMessage($conn, $msg)
    {
        // Handle incoming messages if needed
    }

    public function onClose($conn)
    {
        unset($this->connections[$conn->resourceId]);
        echo "Client disconnected: {$conn->resourceId}\n";
    }

    public function onError($conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    public function broadcast($event, $data)
    {
        foreach ($this->connections as $conn) {
            $conn->send(json_encode([
                'event' => $event,
                'data' => $data
            ]));
        }
    }

    public function startBinanceStream()
    {
        $this->connectToBinance();
        
        $this->loop->addPeriodicTimer(1, function () {
            $this->checkBinanceConnection();
        });
    }

    protected function connectToBinance()
    {
        $socket = new \React\Socket\Connector($this->loop);
        
        $url = 'wss://stream.binance.com:9443/ws/hbarusdt@kline_1m';
        
        $socket->connect($url)->then(
            function (\React\Stream\DuplexStreamInterface $stream) {
                echo "Connected to Binance\n";
                $this->binanceWs = $stream;
                $this->reconnectAttempts = 0;
                
                $stream->on('data', function ($data) {
                    $this->processBinanceData($data);
                });
                
                $stream->on('close', function () {
                    echo "Binance disconnected\n";
                    $this->reconnectBinance();
                });
                
                $stream->on('error', function (\Exception $e) {
                    echo "Binance error: {$e->getMessage()}\n";
                });
            },
            function (\Exception $e) {
                echo "Failed to connect to Binance: {$e->getMessage()}\n";
                $this->reconnectBinance();
            }
        );
    }

    protected function reconnectBinance()
    {
        if ($this->reconnectAttempts >= $this->maxReconnectAttempts) {
            echo "Max reconnect attempts reached\n";
            return;
        }
        
        $this->reconnectAttempts++;
        $delay = min($this->reconnectAttempts * 1000, 30000);
        
        echo "Reconnecting to Binance in {$delay}ms\n";
        
        $this->loop->addTimer($delay / 1000, function () {
            $this->connectToBinance();
        });
    }

    protected function checkBinanceConnection()
    {
        if (!$this->binanceWs || !$this->binanceWs->isReadable()) {
            $this->reconnectBinance();
        }
    }

    protected function processBinanceData($data)
    {
        try {
            $json = json_decode($data, true);
            
            if (!isset($json['k'])) {
                return;
            }
            
            $k = $json['k'];
            
            $candle = [
                'time' => (int) ($k['t'] / 1000),
                'o' => (float) $k['o'],
                'h' => (float) $k['h'],
                'l' => (float) $k['l'],
                'c' => (float) $k['c'],
                'v' => (float) $k['v'],
            ];
            
            // Store in cache for API
            Cache::put('binance_candle_HBARUSDT', $candle, now()->addHour());
            Cache::put('binance_latest_candle', $candle, now()->addMinute());
            
            // Broadcast to connected clients
            $this->broadcast('candle', $candle);
            
        } catch (\Exception $e) {
            // Silent fail
        }
    }
}
