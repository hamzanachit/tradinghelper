<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use React\EventLoop\Loop;
use React\Socket\Connector;
use React\WebSocket\Client;

class BinanceWebSocketSimple extends Command
{
    protected $signature = 'binance:ws {symbol=HBARUSDT} {interval=1m}';
    protected $description = 'Connect to Binance WebSocket and broadcast candle data';

    protected $loop;
    protected $connector;
    protected $connection;
    protected $reconnectAttempts = 0;
    protected $maxReconnectAttempts = 10;

    public function handle()
    {
        $this->info('Starting Binance WebSocket connection...');
        
        $this->loop = Loop::get();
        
        $this->connect();
        
        $this->loop->run();
    }
    
    protected function connect()
    {
        $symbol = strtolower($this->argument('symbol'));
        $interval = $this->argument('interval');
        
        $url = "wss://stream.binance.com:9443/ws/{$symbol}@kline_{$interval}";
        
        $this->info("Connecting to: {$url}");
        
        $this->connector = new Connector([
            'timeout' => 10,
            'dns' => '8.8.8.8',
        ]);
        
        $this->connector->connect($url)->then(
            function (React\Stream\DuplexStreamInterface $stream) {
                $this->connection = $stream;
                $this->reconnectAttempts = 0;
                $this->info('Connected to Binance WebSocket');
                
                $stream->on('data', function ($data) {
                    $this->processMessage($data);
                });
                
                $stream->on('close', function () {
                    $this->warn('Connection closed');
                    $this->reconnect();
                });
                
                $stream->on('error', function ($error) {
                    $this->error('Error: ' . $error->getMessage());
                });
            },
            function ($error) {
                $this->error('Connection failed: ' . $error->getMessage());
                $this->reconnect();
            }
        );
    }
    
    protected function processMessage($message)
    {
        try {
            $data = json_decode($message, true);
            
            if (!isset($data['k'])) {
                return;
            }
            
            $k = $data['k'];
            
            $candle = [
                'time' => (int) ($k['t'] / 1000),
                'o' => (float) $k['o'],
                'h' => (float) $k['h'],
                'l' => (float) $k['l'],
                'c' => (float) $k['c'],
                'v' => (float) $k['v'],
            ];
            
            // Store latest candle in cache for API access
            $symbol = $this->argument('symbol');
            Cache::put("binance_candle_{$symbol}", $candle, now()->addHour());
            Cache::put('binance_latest_candle', $candle, now()->addMinute());
            
            // Broadcast via event
            event(new \App\Events\BinanceCandleUpdated($candle));
            
        } catch (\Exception $e) {
            // Silent fail for message processing
        }
    }
    
    protected function reconnect()
    {
        if ($this->reconnectAttempts >= $this->maxReconnectAttempts) {
            $this->error('Max reconnect attempts reached');
            return;
        }
        
        $this->reconnectAttempts++;
        $delay = min($this->reconnectAttempts * 1000, 30000);
        
        $this->info("Reconnecting in {$delay}ms (attempt {$this->reconnectAttempts})");
        
        $this->loop->addTimer($delay / 1000, function () {
            $this->connect();
        });
    }
}
