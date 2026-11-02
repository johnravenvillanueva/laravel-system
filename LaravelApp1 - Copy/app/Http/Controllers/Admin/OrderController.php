<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product']);

        if ($q = $request->query('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('reference_code', 'like', '%' . $q . '%')
                    ->orWhere('guest_name', 'like', '%' . $q . '%')
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('first_name', 'like', '%' . $q . '%')
                        ->orWhere('last_name', 'like', '%' . $q . '%');
                    });
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $orders = $query->latest('order_date')->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(int $id)
    {
        $order = Order::with(['items.product', 'customer', 'sales'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,preparing,ready,completed,cancelled'],
        ]);

        $previous = $order->status;

        // If already terminal, don't allow regressions
        if (in_array($previous, ['completed', 'cancelled']) && $data['status'] !== $previous) {
            return back()->with('error', 'This order is already ' . $previous . ' and cannot be changed.');
        }

        // If marking as completed, generate a Sale record for reporting
        if ($data['status'] === 'completed' && $previous !== 'completed') {
            \DB::transaction(function () use ($order) {
                // 1. Create the Sale
                $sale = \App\Models\Sale::create([
                    'staff_id'           => auth('admin')->id(), // The staff member who clicked 'Completed'
                    'customer_id'        => $order->customer_id,
                    'reference_order_id' => $order->order_id, // Link it!
                    'sale_date'          => now(),
                    'total_amount'       => $order->total_amount,
                    'payment_method'     => $order->payment_method,
                    'transaction_type'   => 'pre-order',
                ]);

                // 2. Copy the Order Items to Sale Items
                foreach ($order->items as $item) {
                    \App\Models\SaleItem::create([
                        'sale_id'    => $sale->sale_id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal'   => $item->subtotal,
                    ]);
                }

                // 3. Mark payment as paid
                $order->update(['payment_status' => 'paid']);
            });
        }

        // Update the actual order status
        $order->update(['status' => $data['status']]);

        // Notify the customer (if it's a registered customer)
        if ($order->customer_id && $previous !== $data['status']) {
            Notification::create([
                'customer_id' => $order->customer_id,
                'order_id'    => $order->order_id,
                'title'       => 'Order ' . $this->statusLabel($data['status']),
                'message'     => 'Your order ' . $order->reference_code . ' is now ' . $data['status'] . '.',
                'type'        => 'order_update',
                'is_read'     => false,
            ]);
                    // If marking as completed, generate a Sale record for reporting
        if ($data['status'] === 'completed' && $previous !== 'completed') {
            \DB::transaction(function () use ($order) {
                // 1. Create the Sale
                $sale = \App\Models\Sale::create([
                    'staff_id'           => auth('admin')->id(),
                    'customer_id'        => $order->customer_id,
                    'reference_order_id' => $order->order_id,
                    'sale_date'          => now(),
                    'total_amount'       => $order->total_amount,
                    'payment_method'     => $order->payment_method,
                    'transaction_type'   => 'pre-order',
                ]);

                // 2. Copy items AND deduct inventory
                foreach ($order->items as $item) {
                    \App\Models\SaleItem::create([
                        'sale_id'    => $sale->sale_id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal'   => $item->subtotal,
                    ]);

                    // 👇 THIS IS THE NEW LINE 👇
                    // Deduct inventory for this item
                    if ($item->product) {
                        $item->product->deductInventory($item->quantity, 'order', $order->order_id, auth('admin')->id());
                    }
                }

                // 3. Mark payment as paid
                $order->update(['payment_status' => 'paid']);
            });
        }
        }
        

        return back()->with('success', 'Order status updated to ' . ucfirst($data['status']) . '.');
    }


    protected function statusLabel(string $status): string
    {
        return match ($status) {
            'pending'   => 'Received',
            'confirmed' => 'Confirmed',
            'preparing' => 'Being Prepared',
            'ready'     => 'Ready for Pickup',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default     => 'Updated',
        };
    }
}