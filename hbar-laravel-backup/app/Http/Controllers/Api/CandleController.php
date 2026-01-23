<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CandleController extends Controller
{
    public function index(Request $request)
    {
        $symbol = $request->get('symbol', 'HBARUSDT');
        $timeframe = $request->get('timeframe', '1h');
        $limit = min((int)$request->get('limit', 1000), 1000);

        $response = Http::withOptions(['verify' => false])->get("https://api.binance.com/api/v3/klines", [
            'symbol' => $symbol,
            'interval' => $timeframe,
            'limit' => $limit,
        ]);

        if ($response->failed()) {
            return response()->json([]);
        }

        $candles = collect($response->json())->map(function ($c) {
            return [
                'time' => (int)($c[0] / 1000),
                'open' => (float)$c[1],
                'high' => (float)$c[2],
                'low' => (float)$c[3],
                'close' => (float)$c[4],
                'volume' => (float)$c[5],
            ];
        });

        return response()->json($candles->values());
    }

    public function ticker24h(Request $request)
    {
        $symbol = $request->get('symbol', 'HBARUSDT');

        $response = Http::withOptions(['verify' => false])->get("https://api.binance.com/api/v3/ticker/24hr", [
            'symbol' => $symbol,
        ]);

        if ($response->failed()) {
            return response()->json(null);
        }

        $data = $response->json();
        return response()->json([
            'price' => (float)$data['lastPrice'],
            'change' => (float)$data['priceChangePercent'],
            'high' => (float)$data['highPrice'],
            'low' => (float)$data['lowPrice'],
        ]);
    }
}
