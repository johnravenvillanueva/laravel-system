<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $ben  = Admin::where('email', 'ben@issang.test')->first();
        $cara = Admin::where('email', 'cara@issang.test')->first();

        $dan  = Customer::where('email', 'dan@issang.test')->first();
        $ella = Customer::where('email', 'ella@issang.test')->first();

        $macchiato = Product::where('name', 'Issang Macchiato')->first();
        $latte     = Product::where('name', 'Spanish Latte')->first();
        $americano = Product::where('name', 'Americano')->first();
        $matcha    = Product::where('name', 'Matcha Latte')->first();
        $croissant = Product::where('name', 'Butter Croissant')->first();

        // Sale 1 — Walk-in, cash, by Ben
        $sale1 = Sale::create([
            'staff_id'         => $ben->admin_id,
            'customer_id'      => null,
            'sale_date'        => now()->subHours(5),
            'total_amount'     => 255.00,
            'payment_method'   => 'cash',
            'transaction_type' => 'walk-in',
        ]);

        SaleItem::create([
            'sale_id'    => $sale1->sale_id,
            'product_id' => $macchiato->product_id,
            'quantity'   => 1,
            'unit_price' => 145.00,
            'subtotal'   => 145.00,
        ]);
        SaleItem::create([
            'sale_id'    => $sale1->sale_id,
            'product_id' => $americano->product_id,
            'quantity'   => 1,
            'unit_price' => 110.00,
            'subtotal'   => 110.00,
        ]);

        // Sale 2 — Walk-in, gcash, by Cara
        $sale2 = Sale::create([
            'staff_id'         => $cara->admin_id,
            'customer_id'      => null,
            'sale_date'        => now()->subHours(4),
            'total_amount'     => 275.00,
            'payment_method'   => 'gcash',
            'transaction_type' => 'walk-in',
        ]);

        SaleItem::create([
            'sale_id'    => $sale2->sale_id,
            'product_id' => $latte->product_id,
            'quantity'   => 1,
            'unit_price' => 135.00,
            'subtotal'   => 135.00,
        ]);
        SaleItem::create([
            'sale_id'    => $sale2->sale_id,
            'product_id' => $matcha->product_id,
            'quantity'   => 1,
            'unit_price' => 140.00,
            'subtotal'   => 140.00,
        ]);

        // Sale 3 — Pre-order pickup (links to IM-00005 which is completed)
        $completedOrder = Order::where('reference_code', 'IM-00005')->first();

        $sale3 = Sale::create([
            'staff_id'           => $ben->admin_id,
            'customer_id'        => $ella->customer_id,
            'reference_order_id' => $completedOrder->order_id,
            'sale_date'          => now()->subDay()->addHours(1),
            'total_amount'       => 270.00,
            'payment_method'     => 'gcash',
            'transaction_type'   => 'pre-order',
        ]);

        SaleItem::create([
            'sale_id'    => $sale3->sale_id,
            'product_id' => $latte->product_id,
            'quantity'   => 2,
            'unit_price' => 135.00,
            'subtotal'   => 270.00,
        ]);

        // Sale 4 — Walk-in, cash, by Cara
        $sale4 = Sale::create([
            'staff_id'         => $cara->admin_id,
            'customer_id'      => $dan->customer_id,
            'sale_date'        => now()->subHours(2),
            'total_amount'     => 170.00,
            'payment_method'   => 'cash',
            'transaction_type' => 'walk-in',
        ]);

        SaleItem::create([
            'sale_id'    => $sale4->sale_id,
            'product_id' => $croissant->product_id,
            'quantity'   => 2,
            'unit_price' => 85.00,
            'subtotal'   => 170.00,
        ]);

        // Sale 5 — Walk-in, gcash, by Ben
        $sale5 = Sale::create([
            'staff_id'         => $ben->admin_id,
            'customer_id'      => null,
            'sale_date'        => now()->subMinutes(30),
            'total_amount'     => 145.00,
            'payment_method'   => 'gcash',
            'transaction_type' => 'walk-in',
        ]);

        SaleItem::create([
            'sale_id'    => $sale5->sale_id,
            'product_id' => $macchiato->product_id,
            'quantity'   => 1,
            'unit_price' => 145.00,
            'subtotal'   => 145.00,
        ]);
    }
}