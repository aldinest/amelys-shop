<?php

namespace App\Http\Controllers;

use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->get();

        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum->total;

        $statusCount = [
            'pending' => $orders->where('status', 'pending')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'shipped' => $orders->where('status', 'shipped')->count(),
            'completed' => $orders->where('status', 'completed')->count(),
        ];

        $recentOrders = Order::orderBy('order_date', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'statusCount',
            'recentOrders'
        ));
    }
}
