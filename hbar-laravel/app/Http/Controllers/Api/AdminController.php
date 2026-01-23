<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $admin = \App\Models\Admin::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $admin->last_login = now();
        $admin->save();

        $token = $request->session()->token();
        
        return response()->json([
            'token' => $token,
            'admin' => [
                'id' => $admin->id,
                'email' => $admin->email,
                'name' => $admin->name,
                'role' => $admin->role,
            ],
        ]);
    }

    public function stats(Request $request)
    {
        $totalUsers = \App\Models\User::count();
        $activeSubs = \App\Models\UserSubscription::where('status', 'active')->count();
        
        $totalRevenue = \App\Models\Payment::where('status', 'completed')->sum('amount');
        
        $monthAgo = now()->subMonth();
        $monthlyRevenue = \App\Models\Payment::where('status', 'completed')
            ->where('created_at', '>=', $monthAgo)
            ->sum('amount');

        $subsByPlan = \App\Models\UserSubscription::with('plan')
            ->where('status', 'active')
            ->get()
            ->groupBy('plan.name')
            ->map(fn($group) => $group->count());

        $recentUsers = \App\Models\User::orderBy('created_at', 'desc')->limit(5)->get();
        $recentPayments = \App\Models\Payment::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'totalUsers' => $totalUsers,
            'activeSubs' => $activeSubs,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'planStats' => $subsByPlan,
            'recentUsers' => $recentUsers,
            'recentPayments' => $recentPayments,
        ]);
    }

    public function users(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);
        $search = $request->get('search', '');

        $query = \App\Models\User::with('subscription.plan');

        if ($search) {
            $query->where('email', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%");
        }

        $total = $query->count();
        $users = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'totalPages' => ceil($total / $limit),
        ]);
    }

    public function payments(Request $request)
    {
        $payments = \App\Models\Payment::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        return response()->json($payments);
    }

    public function plans(Request $request)
    {
        return response()->json(\App\Models\Plan::orderBy('sort_order')->get());
    }
}
