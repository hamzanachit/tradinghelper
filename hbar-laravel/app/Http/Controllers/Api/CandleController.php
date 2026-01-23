<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CandleController extends Controller
{
    public function index(Request $request)
    {
        $symbol = $request->get('symbol', 'HBARUSDT');
        $timeframe = $request->get('timeframe', '1h');
        $limit = min((int)$request->get('limit', 1000), 1000);

        $cacheKey = "candles_{$symbol}_{$timeframe}_{$limit}";
        
        // Try cache first for real-time data
        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            if ($cachedData && count($cachedData) > 0) {
                return response()->json($cachedData);
            }
        }

        // Fallback to direct API call
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
                'o' => (float)$c[1],
                'h' => (float)$c[2],
                'l' => (float)$c[3],
                'c' => (float)$c[4],
                'v' => (float)$c[5],
            ];
        });

        // Cache for 5 minutes
        Cache::put($cacheKey, $candles->values(), now()->addMinutes(5));

        return response()->json($candles->values());
    }

    public function latest(Request $request)
    {
        $symbol = $request->get('symbol', 'HBARUSDT');
        
        // Get latest candle from WebSocket cache
        $cachedCandle = Cache::get("binance_candle_{$symbol}");
        
        if ($cachedCandle) {
            return response()->json($cachedCandle);
        }
        
        return response()->json(null);
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
