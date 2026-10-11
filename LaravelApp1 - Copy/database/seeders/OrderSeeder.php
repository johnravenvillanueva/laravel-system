<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $dan   = Customer::where('email', 'dan@issang.test')->first();
        $ella  = Customer::where('email', 'ella@issang.test')->first();
        $guest = Customer::where('phone', '0917 888 8888')->first();

        $macchiato = Product::where('name', 'Issang Macchiato')->first();
        $latte     = Product::where('name', 'Spanish Latte')->first();
        $croissant = Product::where('name', 'Butter Croissant')->first();
        $matcha    = Product::where('name', 'Matcha Latte')->first();

        // Order 1 — Dan, pending
        $order1 = Order::create([
            'reference_code' => 'IM-00001',
            'customer_id'    => $dan->customer_id,
            'order_date'     => now()->subHours(3),
            'pickup_date'    => now()->addDay()->toDateString(),
            'pickup_time'    => '10:00',
            'status'         => 'pending',
            'total_amount'   => 290.00,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'notes'          => 'Please make it extra hot.',
        ]);

        OrderItem::create([
            'order_id'             => $order1->order_id,
            'product_id'           => $macchiato->product_id,
            'quantity'             => 1,
            'unit_price'           => 145.00,
            'subtotal'             => 145.00,
            'special_instructions' => 'Extra shot',
        ]);
        OrderItem::create([
            'order_id'             => $order1->order_id,
            'product_id'           => $latte->product_id,
            'quantity'             => 1,
            'unit_price'           => 145.00,
            'subtotal'             => 145.00,
        ]);

        // Order 2 — Ella, confirmed
        $order2 = Order::create([
            'reference_code' => 'IM-00002',
            'customer_id'    => $ella->customer_id,
            'order_date'     => now()->subHours(2),
            'pickup_date'    => now()->toDateString(),
            'pickup_time'    => '14:30',
            'status'         => 'confirmed',
            'total_amount'   => 225.00,
            'payment_method' => 'gcash',
            'payment_status' => 'pending',
        ]);

        OrderItem::create([
            'order_id'             => $order2->order_id,
            'product_id'           => $matcha->product_id,
            'quantity'             => 1,
            'unit_price'           => 140.00,
            'subtotal'             => 140.00,
        ]);
        OrderItem::create([
            'order_id'             => $order2->order_id,
            'product_id'           => $croissant->product_id,
            'quantity'             => 1,
            'unit_price'           => 85.00,
            'subtotal'             => 85.00,
        ]);

        // Order 3 — Guest, preparing
        $order3 = Order::create([
            'reference_code' => 'IM-00003',
            'customer_id'    => null,
            'guest_name'     => 'Guest Walker',
            'guest_phone'    => '0917 888 8888',
            'guest_email'    => null,
            'order_date'     => now()->subHour(),
            'pickup_date'    => now()->toDateString(),
            'pickup_time'    => '15:45',
            'status'         => 'preparing',
            'total_amount'   => 220.00,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        OrderItem::create([
            'order_id'   => $order3->order_id,
            'product_id' => $latte->product_id,
            'quantity'   => 1,
            'unit_price' => 135.00,
            'subtotal'   => 135.00,
        ]);
        OrderItem::create([
            'order_id'   => $order3->order_id,
            'product_id' => $croissant->product_id,
            'quantity'   => 1,
            'unit_price' => 85.00,
            'subtotal'   => 85.00,
        ]);

        // Order 4 — Dan, ready for pickup
        $order4 = Order::create([
            'reference_code' => 'IM-00004',
            'customer_id'    => $dan->customer_id,
            'order_date'     => now()->subHours(4),
            'pickup_date'    => now()->toDateString(),
            'pickup_time'    => '13:00',
            'status'         => 'ready',
            'total_amount'   => 145.00,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        OrderItem::create([
            'order_id'   => $order4->order_id,
            'product_id' => $macchiato->product_id,
            'quantity'   => 1,
            'unit_price' => 145.00,
            'subtotal'   => 145.00,
        ]);

        // Order 5 — Ella, completed
        $order5 = Order::create([
            'reference_code' => 'IM-00005',
            'customer_id'    => $ella->customer_id,
            'order_date'     => now()->subDay(),
            'pickup_date'    => now()->subDay()->toDateString(),
            'pickup_time'    => '09:00',
            'status'         => 'completed',
            'total_amount'   => 270.00,
            'payment_method' => 'gcash',
            'payment_status' => 'paid',
        ]);

        OrderItem::create([
            'order_id'   => $order5->order_id,
            'product_id' => $latte->product_id,
            'quantity'   => 2,
            'unit_price' => 135.00,
            'subtotal'   => 270.00,
        ]);
    }
}