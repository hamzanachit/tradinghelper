<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('binance:ws {symbol=HBARUSDT} {interval=1m}', function () {
    $this->comment('Starting Binance WebSocket...');
})->purpose('Binance WebSocket command placeholder');
