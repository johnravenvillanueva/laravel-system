<?php

namespace App\Http\Controllers;

use App\Support\DemoData;
use Illuminate\Http\Request;

class GuestOrderController extends Controller
{
    public function checkout(Request $request)
    {
        $cartItems = $request->session()->get('guest_cart', []);
        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['qty']);

        return view('customer.guest-checkout', compact('cartItems', 'subtotal'));
    }

    public function place(Request $request)
    {
        $data = $request->validate([
            'guest_name'     => ['required','string','max:100'],
            'guest_phone'    => ['required','string','max:15'],
            'guest_email'    => ['nullable','email','max:100'],
            'pickup_date'    => ['required','date','after_or_equal:today'],
            'pickup_time'    => ['required'],
            'payment_method' => ['required','in:cash,gcash'],
            'notes'          => ['nullable','string','max:500'],
        ]);

        // TODO: save order to DB when ready.
        $request->session()->put('guest_last_order', [
            'ref'        => 'IM-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT),
            'name'       => $data['guest_name'],
            'pickup'     => $data['pickup_date'] . ' ' . $data['pickup_time'],
            'payment'    => $data['payment_method'],
        ]);

        $request->session()->forget('guest_cart');

        return redirect()->route('customer.guest.confirm');
    }

    public function confirm(Request $request)
    {
        $order = $request->session()->get('guest_last_order', [
            'ref' => 'IM-00000',
            'name' => 'Guest',
            'pickup' => now()->toDateString() . ' 15:30',
            'payment' => 'cash',
        ]);

        return view('customer.guest-order-confirm', compact('order'));
    }
}