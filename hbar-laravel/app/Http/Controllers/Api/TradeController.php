<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index(Request $request)
    {
        $trades = $request->user()->trades()
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
        return response()->json($trades);
    }

    public function store(Request $request)
    {
        $trade = $request->user()->trades()->create([
            'type' => $request->type,
            'symbol' => $request->symbol ?? 'HBARUSDT',
            'amount' => $request->amount,
            'price' => $request->price,
            'pnl' => $request->pnl ?? 0,
            'note' => $request->note ?? '',
            'timeframe' => $request->timeframe ?? '1h',
        ]);
        return response()->json($trade);
    }

    public function destroy(Request $request, $id)
    {
        $trade = $request->user()->trades()->findOrFail($id);
        $trade->delete();
        return response()->json(['success' => true]);
    }
}
