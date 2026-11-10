<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Support\GuestSmsSimulator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $order = Order::with(['items.product', 'customer', 'sales', 'guestSmsNotifications'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,preparing,ready,completed,cancelled'],
        ]);

        $error = DB::transaction(function () use ($id, $data) {
            $order = Order::with(['items.product'])
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();
            $previous = $order->status;

            if (in_array($previous, ['completed', 'cancelled'], true) && $data['status'] !== $previous) {
                return 'This order is already '.$previous.' and cannot be changed.';
            }

            if ($previous === $data['status']) {
                return null;
            }

            if ($data['status'] === 'completed') {
                $sale = \App\Models\Sale::create([
                    'staff_id' => auth('admin')->id(),
                    'customer_id' => $order->customer_id,
                    'reference_order_id' => $order->order_id,
                    'sale_date' => now(),
                    'total_amount' => $order->total_amount,
                    'payment_method' => $order->payment_method,
                    'transaction_type' => 'pre-order',
                ]);

                foreach ($order->items as $item) {
                    \App\Models\SaleItem::create([
                        'sale_id' => $sale->sale_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                    ]);

                    if ($item->product) {
                        $item->product->deductInventory(
                            $item->quantity,
                            'order',
                            $order->order_id,
                            auth('admin')->id()
                        );
                    }
                }

                $order->payment_status = 'paid';
            }

            $order->status = $data['status'];
            $order->save();

            if ($order->customer_id) {
                Notification::create([
                    'customer_id' => $order->customer_id,
                    'order_id' => $order->order_id,
                    'title' => 'Order '.$this->statusLabel($data['status']),
                    'message' => 'Your order '.$order->reference_code.' is now '.$data['status'].'.',
                    'type' => 'order_update',
                    'is_read' => false,
                ]);
            } elseif ($order->guest_phone) {
                GuestSmsSimulator::orderStatusChanged($order, $this->statusLabel($data['status']));
            }

            return null;
        });

        if ($error) {
            return back()->with('error', $error);
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