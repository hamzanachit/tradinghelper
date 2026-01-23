<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->watchlist()
            ->orderBy('created_at', 'desc')
            ->get());
    }

    public function store(Request $request)
    {
        $exists = $request->user()->watchlist()
            ->where('symbol', $request->symbol)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Already in watchlist'], 400);
        }

        $watchlist = $request->user()->watchlist()->create([
            'symbol' => $request->symbol,
        ]);
        return response()->json($watchlist);
    }

    public function destroy(Request $request, $id)
    {
        $item = $request->user()->watchlist()->findOrFail($id);
        $item->delete();
        return response()->json(['success' => true]);
    }
}
