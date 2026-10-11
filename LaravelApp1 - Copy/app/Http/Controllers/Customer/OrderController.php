<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Support\DemoData;

class OrderController extends Controller
{
    public function index()
    {
        return view('customer.orders', ['orders' => DemoData::orders()]);
    }

    public function show(int $id)
    {
        $order = DemoData::orders()->firstWhere('id', $id) ?? DemoData::orders()->first();
        return view('customer.order-show', ['order' => $order]);
    }

    public function cart()
    {
        return view('customer.cart', [
            'cart' => (object)['items' => collect()],
            'subtotal' => 0,
        ]);
    }

    public function checkout()
    {
        return view('customer.checkout', [
            'cart' => (object)['items' => collect()],
            'subtotal' => 0,
        ]);
    }

    public function notifications()
    {
        return view('customer.notifications', ['notifications' => DemoData::notifications()]);
    }

    public function profile()
    {
        $user = (object)[
            'first_name' => 'Guest', 'last_name' => 'User',
            'email' => 'guest@issang.test', 'phone' => '',
            'address' => '', 'role' => 'customer',
        ];
        return view('customer.profile', ['user' => $user]);
    }
}