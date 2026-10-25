<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * The currently logged-in customer.
     * (Assumes 'customer' guard is active — we protect the route in Phase 3.)
     */
    protected function customer()
    {
        return Auth::guard('customer')->user();
    }

    /* -------------------------------------------------
     |  Orders
     |-------------------------------------------------*/

    public function index()
    {
        $orders = Order::with(['items.product'])
            ->where('customer_id', $this->customer()->customer_id)
            ->latest('order_date')
            ->get();

        return view('customer.orders', compact('orders'));
    }

    public function show(int $id)
    {
        $order = Order::with(['items.product'])
            ->where('customer_id', $this->customer()->customer_id)
            ->findOrFail($id);

        return view('customer.order-show', compact('order'));
    }

    /* -------------------------------------------------
     |  Cart (session-based for now — DB cart comes later)
     |-------------------------------------------------*/

    public function cart(Request $request)
    {
        $cartItems = $request->session()->get('customer_cart', []);
        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['qty']);

        // Wrap in the shape the Blade expects
        $cart = (object) [
            'items' => collect($cartItems)->map(function ($item, $productId) {
                return (object) [
                    'id'                   => $productId,
                    'product'              => (object) [
                        'product_id' => $productId,
                        'name'       => $item['name'],
                        'price'      => $item['price'],
                    ],
                    'quantity'             => $item['qty'],
                    'special_instructions' => $item['note'] ?? '',
                    'image_url'            => $item['image_url'] ?? null,
                ];
            })->values(),
        ];

        return view('customer.cart', compact('cart', 'subtotal'));
    }

    public function addToCart(Request $request, int $productId)
    {
        $product = Product::available()->findOrFail($productId);
        $qty = max(1, (int) $request->input('quantity', 1));
        $note = $request->input('special_instructions', '');

        $cart = $request->session()->get('customer_cart', []);

        if (isset($cart[$productId])) {
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
                'image_url' => $product->image_url,
            ];
        }

        $request->session()->put('customer_cart', $cart);

        return back()->with('success', $product->name . ' added to cart.');
    }

    public function updateCart(Request $request, int $productId)
    {
        $qty = max(1, (int) $request->input('quantity', 1));
        $cart = $request->session()->get('customer_cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] = $qty;
            $request->session()->put('customer_cart', $cart);
        }

        return back();
    }

    public function removeFromCart(Request $request, int $productId)
    {
        $cart = $request->session()->get('customer_cart', []);
        unset($cart[$productId]);
        $request->session()->put('customer_cart', $cart);

        return back()->with('success', 'Item removed.');
    }

    public function checkout(Request $request)
    {
        $cartItems = $request->session()->get('customer_cart', []);
        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['qty']);

        $cart = (object) [
            'items' => collect($cartItems)->map(function ($item, $productId) {
                return (object) [
                    'product'  => (object) ['name' => $item['name'], 'price' => $item['price']],
                    'quantity' => $item['qty'],
                ];
            })->values(),
        ];

        return view('customer.checkout', compact('cart', 'subtotal'));
    }

    public function placeOrder(Request $request)
    {        $data = $request->validate([
            'pickup_date'    => ['required', 'date', 'after_or_equal:today'],
            'pickup_time'    => ['required', function ($attribute, $value, $fail) use ($request) {
                $pickupDateTime = \Carbon\Carbon::parse($request->pickup_date . ' ' . $value);
                
                if ($pickupDateTime->isToday() && $pickupDateTime->lessThan(now()->addMinutes(15))) {
                    $fail('The pickup time must be at least 15 minutes from now.');
                }
            }],
            'payment_method' => ['required', 'in:cash,gcash'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);
        $cartItems = $request->session()->get('customer_cart', []);

        if (empty($cartItems)) {
            return back()->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['qty']);
        $customer = $this->customer();

        $order = \DB::transaction(function () use ($data, $cartItems, $subtotal, $customer) {
           
            $order = Order::create([
                'reference_code' => Order::generateReferenceCode(),
                'customer_id'    => $customer->customer_id,
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
                \App\Models\OrderItem::create([
                    'order_id'             => $order->order_id,
                    'product_id'           => $productId,
                    'quantity'             => $item['qty'],
                    'unit_price'           => $item['price'],
                    'subtotal'             => $item['price'] * $item['qty'],
                    'special_instructions' => $item['note'] ?? null,
                ]);
            }

            // Notify the customer
            Notification::create([
                'customer_id' => $customer->customer_id,
                'order_id'    => $order->order_id,
                'title'       => 'Order Placed',
                'message'     => 'Your pre-order ' . $order->reference_code . ' has been received.',
                'type'        => 'order_update',
                'is_read'     => false,
            ]);
            Notification::create([
                'admin_id' => 1, // Sends to the main admin. Or loop through all admins.
                'order_id' => $order->order_id,
                'title'    => 'New Pre-Order Received',
                'message'  => 'Order ' . $order->reference_code . ' was placed for pickup on ' . $order->pickup_date,
                'type'     => 'new_order',
                'is_read'  => false,
            ]);

            return $order;
        });

        $request->session()->forget('customer_cart');

        return redirect()->route('customer.orders')
            ->with('success', 'Order ' . $order->reference_code . ' placed successfully!');
    }

    public function notifications()
    {
        $notifications = Notification::where('customer_id', $this->customer()->customer_id)
            ->latest()
            ->get();

        return view('customer.notifications', compact('notifications'));
    }

    public function markAllNotificationsRead()
    {
        Notification::where('customer_id', $this->customer()->customer_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }


    public function profile()
    {
        $user = $this->customer();
        return view('customer.profile', compact('user'));
    }

   public function updateProfile(Request $request)
    {
        $customer = $this->customer();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => ['required', 'email', 'max:100', 'unique:customers,email,' . $customer->customer_id . ',customer_id'],
            'phone'      => ['nullable', 'string', 'max:15'],
            'address'    => ['nullable', 'string'],
            'avatar'     => ['nullable', 'image', 'max:2048'], // <-- ADD THIS
        ]);

        // Handle the avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($customer->avatar && \Storage::disk('public')->exists(str_replace('storage/', '', $customer->avatar))) {
                \Storage::disk('public')->delete(str_replace('storage/', '', $customer->avatar));
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = 'storage/' . $path;
        }

        $customer->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function changePassword(Request $request)
    {
        $customer = $this->customer();

        $data = $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! \Hash::check($data['current_password'], $customer->password_hash)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $customer->update(['password_hash' => \Hash::make($data['password'])]);

        return back()->with('success', 'Password changed.');
    }
}