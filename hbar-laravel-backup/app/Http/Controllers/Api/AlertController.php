<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->alerts()
            ->orderBy('created_at', 'desc')
            ->get());
    }

    public function store(Request $request)
    {
        $alert = $request->user()->alerts()->create([
            'symbol' => $request->symbol ?? 'HBARUSDT',
            'price' => $request->price,
            'triggered' => false,
        ]);
        return response()->json($alert);
    }

    public function destroy(Request $request, $id)
    {
        $alert = $request->user()->alerts()->findOrFail($id);
        $alert->delete();
        return response()->json(['success' => true]);
    }
}
