<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's total sales
        $todaySales = Sale::whereDate('sale_date', today())->sum('total_amount');

        // Pending orders (waiting to be confirmed)
        $pendingOrders = Order::where('status', 'pending')->count();

        // Total products
        $productCount = Product::count();

        // Low stock items (quantity <= min_threshold)
        $lowStockItems = InventoryItem::whereColumn('quantity', '<=', 'min_threshold')
            ->orderBy('quantity')
            ->get();

        // Recent 5 orders (newest first), with customer info
        $recentOrders = Order::with('customer')
            ->latest('order_date')
            ->take(5)
            ->get();

        //last 7 days sales for a mini chart
        $chartData = Sale::select(
                DB::raw('DATE(sale_date) as day'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('sale_date', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        return view('admin.dashboard', [
            'todaySales'    => $todaySales,
            'pendingOrders' => $pendingOrders,
            'productCount'  => $productCount,
            'lowStockItems' => $lowStockItems,
            'lowStockCount' => $lowStockItems->count(),
            'recentOrders'  => $recentOrders,
            'chartData'     => $chartData,
        ]);
    }
        public function notifications()
    {
        $notifications = \App\Models\Notification::where('admin_id', auth('admin')->id())
            ->latest()
            ->get();

        return view('admin.notifications', compact('notifications'));
    }

    public function markNotificationsRead()
    {
        \App\Models\Notification::where('admin_id', auth('admin')->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}