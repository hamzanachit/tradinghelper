<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http\EventLoop\Loop;
use React;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use Ratchet\ConnectionInterface;

class BinanceWebSocket extends Command
{
    protected $signature = 'binance:websocket {symbol=HBARUSDT} {interval=1m}';
    protected $description = 'Connect to Binance WebSocket and broadcast candle data';

    protected $connection;
    protected $loop;
    protected $connector;
    protected $reconnectAttempts = 0;
    protected $maxReconnectAttempts = 10;
    protected $reconnectDelay = 3000;

    public function handle()
    {
        $this->info('Starting Binance WebSocket connection...');
        
        $this->loop = Loop::get();
        
        $this->connector = new Connector($this->loop);
        
        $this->connect();
        
        $this->loop->run();
    }
    
    protected function connect()
    {
        $symbol = $this->argument('symbol');
        $interval = $this->argument('interval');
        
        $url = "wss://stream.binance.com:9443/ws/" . strtolower($symbol) . "@kline_" . $interval;
        
        $this->info("Connecting to: {$url}");
        
        ($this->connector)($url)->then(
            function (WebSocket $connection) {
                $this->connection = $connection;
                $this->reconnectAttempts = 0;
                $this->info('Connected to Binance WebSocket');
                
                $connection->on('message', function ($message) {
                    $this->processMessage($message);
                });
                
                $connection->on('close', function () {
                    $this->warn('Connection closed, attempting to reconnect...');
                    $this->reconnect();
                });
                
                $connection->on('error', function ($error) {
                    $this->error('WebSocket error: ' . $error->getMessage());
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
            Cache::put('binance_candle_' . $this->argument('symbol'), $candle, now()->addHour());
            
            // Broadcast via Reverb
            if (class_exists(\Laravel\Reverb\Reverb::class)) {
                try {
                    \Laravel\Reverb\Reverb::channel('candles.' . $this->argument('symbol'))
                        ->broadcast(['candle' => $candle]);
                } catch (\Exception $e) {
                    // Reverb not configured yet
                }
            }
            
            // Alternative: Store in Redis for polling
            Cache::put('binance_latest_candle', $candle, now()->addMinute());
            
        } catch (\Exception $e) {
            $this->error('Error processing message: ' . $e->getMessage());
        }
    }
    
    protected function reconnect()
    {
        if ($this->reconnectAttempts >= $this->maxReconnectAttempts) {
            $this->error('Max reconnect attempts reached. Exiting.');
            return;
        }
        
        $this->reconnectAttempts++;
        $delay = $this->reconnectDelay * $this->reconnectAttempts;
        
        $this->info("Reconnecting in {$delay}ms (attempt {$this->reconnectAttempts}/{$this->maxReconnectAttempts})");
        
        $this->loop->addTimer($delay / 1000, function () {
            $this->connect();
        });
    }
}
