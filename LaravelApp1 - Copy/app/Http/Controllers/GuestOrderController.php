<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestOrderController extends Controller
{
    /**
     * Show the guest checkout page.
     *
     * The guest cart lives in session under key 'guest_cart':
     *   ['product_id' => ['name' => ..., 'price' => ..., 'qty' => ..., 'note' => ...], ...]
     */
    public function checkout(Request $request)
    {
        $cartItems = $request->session()->get('guest_cart', []);
        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['qty']);

        return view('customer.guest-checkout', compact('cartItems', 'subtotal'));
    }

    /**
     * Add an item to the guest cart.
     */
    public function addToCart(Request $request, int $productId)
    {
        $product = Product::available()->findOrFail($productId);
        $qty = max(1, (int) $request->input('quantity', 1));
        $note = $request->input('special_instructions', '');

        $cart = $request->session()->get('guest_cart', []);

        if (isset($cart[$productId])) {
            // Same product already in cart → increment
            $cart[$productId]['qty'] += $qty;
            if ($note) {
                $cart[$productId]['note'] = $note;
            }
        } else {
            $cart[$productId] = [
                'name'  => $product->name,
                'price' => (float) $product->price,
                'qty'   => $qty,
                'note'  => $note,
            ];
        }

        $request->session()->put('guest_cart', $cart);

        return back()->with('success', $product->name . ' added to cart.');
    }

    /**
     * Remove an item from the guest cart.
     */
    public function removeFromCart(Request $request, int $productId)
    {
        $cart = $request->session()->get('guest_cart', []);
        unset($cart[$productId]);
        $request->session()->put('guest_cart', $cart);

        return back()->with('success', 'Item removed.');
    }

    /**
     * Place the guest order — writes to orders + order_items.
     */
    public function place(Request $request)
    {
        $data = $request->validate([
            'guest_name'     => ['required', 'string', 'max:100'],
            'guest_phone'    => ['required', 'string', 'max:15'],
            'guest_email'    => ['nullable', 'email', 'max:100'],
            'pickup_date'    => ['required', 'date', 'after_or_equal:today'],
            'pickup_time'    => ['required', function ($attribute, $value, $fail) use ($request) {
                // Combine the date and time to check if it's in the past
                $pickupDateTime = \Carbon\Carbon::parse($request->pickup_date . ' ' . $value);
                
                // If the date is today, the time must be at least 15 minutes in the future
                if ($pickupDateTime->isToday() && $pickupDateTime->lessThan(now()->addMinutes(15))) {
                    $fail('The pickup time must be at least 15 minutes from now.');
                }
            }],
            'payment_method' => ['required', 'in:cash,gcash'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);
        $cartItems = $request->session()->get('guest_cart', []);

        if (empty($cartItems)) {
            return back()->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['qty']);

        // Wrap in a transaction — either the order + items all save, or nothing does
        $order = DB::transaction(function () use ($data, $cartItems, $subtotal) {
            $order = Order::create([
                'reference_code' => Order::generateReferenceCode(),
                'customer_id'    => null, // guest — no customer row
                'guest_name'     => $data['guest_name'],
                'guest_phone'    => $data['guest_phone'],
                'guest_email'    => $data['guest_email'] ?? null,
                'order_date'     => now(),
                'pickup_date'    => $data['pickup_date'],
                'pickup_time'    => $data['pickup_time'],
                'status'         => 'pending',
                'total_amount'   => $subtotal,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($cartItems as $productId => $item) {
                OrderItem::create([
                    'order_id'             => $order->order_id,
                    'product_id'           => $productId,
                    'quantity'             => $item['qty'],
                    'unit_price'           => $item['price'],
                    'subtotal'             => $item['price'] * $item['qty'],
                    'special_instructions' => $item['note'] ?? null,
                ]);
                Notification::create([
                'admin_id' => 1, // Sends to the main admin. Or loop through all admins.
                'order_id' => $order->order_id,
                'title'    => 'New Pre-Order Received',
                'message'  => 'Order ' . $order->reference_code . ' was placed for pickup on ' . $order->pickup_date,
                'type'     => 'new_order',
                'is_read'  => false,
            ]);
            }

            return $order;
        });

        // Save last-order info in session for the confirmation page
        $request->session()->put('guest_last_order', [
            'ref'     => $order->reference_code,
            'name'    => $order->guest_name,
            'pickup'  => $order->pickup_date->format('M d, Y') . ' · ' . $order->pickup_time,
            'payment' => ucfirst($order->payment_method),
            'total'   => number_format((float) $order->total_amount, 2),
        ]);

        // Clear guest cart
        $request->session()->forget('guest_cart');

        return redirect()->route('customer.guest.confirm');
    }

    /**
     * Confirmation page after placing a guest order.
     */
    public function confirm(Request $request)
    {
        $order = $request->session()->get('guest_last_order');

        if (! $order) {
            return redirect()->route('menu');
        }

        return view('customer.guest-order-confirm', compact('order'));
    }
}