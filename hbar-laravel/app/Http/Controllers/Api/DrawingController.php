<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DrawingController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->drawings);
    }

    public function store(Request $request)
    {
        $drawing = $request->user()->drawings()->create([
            'type' => $request->type,
            'price' => $request->price,
            'time' => $request->time,
            'end_time' => $request->endTime,
            'color' => $request->color ?? '#2962ff',
        ]);
        return response()->json($drawing);
    }

    public function destroy(Request $request, $id)
    {
        $drawing = $request->user()->drawings()->findOrFail($id);
        $drawing->delete();
        return response()->json(['success' => true]);
    }

    public function clear(Request $request)
    {
        $request->user()->drawings()->delete();
        return response()->json(['success' => true]);
    }
}
