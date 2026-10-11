<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = DemoData::orders();
        if ($status = $request->query('status')) {
            $orders = $orders->where('status', $status);
        }
        if ($q = $request->query('q')) {
            $orders = $orders->filter(fn($o) => str_contains((string)$o->id, $q));
        }

        return view('admin.orders.index', ['orders' => $orders->values()]);
    }

    public function show(int $id)
    {
        $order = DemoData::orders()->firstWhere('id', $id) ?? DemoData::orders()->first();
        return view('admin.orders.show', ['order' => $order]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => ['required','in:pending,confirmed,preparing,ready,completed,cancelled'],
        ]);

        return redirect()->route('admin.orders.show', $id)->with('success', 'Order status updated.');
    }
}