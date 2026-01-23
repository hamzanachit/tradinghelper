<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BinanceCandleUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $candle;
    public $symbol;

    public function __construct(array $candle, string $symbol = 'HBARUSDT')
    {
        $this->candle = $candle;
        $this->symbol = $symbol;
    }

    public function broadcastOn()
    {
        return new Channel('candles.' . $this->symbol);
    }

    public function broadcastAs()
    {
        return 'candle';
    }
}
