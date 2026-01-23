<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $trades = $user->trades;

        if ($trades->isEmpty()) {
            return response()->json([
                'totalValue' => 10000,
                'positions' => [],
                'pnl' => 0,
                'winRate' => 0,
            ]);
        }

        $positions = [];
        $totalCost = 0;
        $totalValue = 0;
        $closedPnL = 0;
        $wins = 0;
        $closedCount = 0;

        foreach ($trades as $trade) {
            if ($trade->type === 'buy') {
                if (!isset($positions[$trade->symbol])) {
                    $positions[$trade->symbol] = ['amount' => 0, 'avgPrice' => 0];
                }
                $currentValue = $positions[$trade->symbol]['amount'] * $positions[$trade->symbol]['avgPrice'];
                $newValue = $currentValue + ($trade->amount * $trade->price);
                $positions[$trade->symbol]['amount'] += $trade->amount;
                $positions[$trade->symbol]['avgPrice'] = $newValue / $positions[$trade->symbol]['amount'];
                $totalCost += $trade->amount * $trade->price;
            } elseif ($trade->type === 'sell') {
                if (isset($positions[$trade->symbol])) {
                    $sellValue = $trade->amount * $trade->price;
                    $costBasis = $trade->amount * $positions[$trade->symbol]['avgPrice'];
                    $totalValue += $sellValue;
                    $closedPnL += $sellValue - $costBasis;
                    if ($sellValue > $costBasis) $wins++;
                    $closedCount++;
                    $positions[$trade->symbol]['amount'] -= $trade->amount;
                }
            } elseif ($trade->type === 'close') {
                $closedPnL += $trade->pnl;
                if ($trade->pnl > 0) $wins++;
                $closedCount++;
            }
        }

        $openPositions = collect($positions)
            ->filter(fn($pos) => $pos['amount'] > 0.0001)
            ->map(fn($pos, $symbol) => [
                'symbol' => $symbol,
                'amount' => $pos['amount'],
                'avgPrice' => $pos['avgPrice'],
            ])
            ->values();

        return response()->json([
            'totalValue' => $totalCost + $totalValue,
            'positions' => $openPositions,
            'pnl' => $closedPnL,
            'winRate' => $closedCount > 0 ? round(($wins / $closedCount) * 100) : 0,
        ]);
    }
}
