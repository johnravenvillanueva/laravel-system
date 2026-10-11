<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData;

class DashboardController extends Controller
{
    public function index()
    {
        $sales = DemoData::sales();
        $orders = DemoData::orders();
        $inventory = DemoData::inventory();

        return view('admin.dashboard', [
            'todaySales'      => $sales->sum('total_amount'),
            'pendingOrders'   => $orders->where('status', 'pending')->count(),
            'productCount'    => DemoData::products()->count(),
            'lowStockCount'   => $inventory->filter(fn($i) => $i->quantity <= $i->min_threshold)->count(),
            'recentOrders'    => $orders,
            'lowStockItems'   => $inventory->filter(fn($i) => $i->quantity <= $i->min_threshold)->values(),
        ]);
    }
}