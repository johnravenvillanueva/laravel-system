<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name'          => 'Espresso Beans',
                'category'      => 'Coffee Bean',
                'quantity'      => 12.00,
                'unit'          => 'kg',
                'min_threshold' => 5.00,
            ],
            [
                'name'          => 'Whole Milk',
                'category'      => 'Dairy',
                'quantity'      => 3.00,   // low — will trigger low-stock badge
                'unit'          => 'L',
                'min_threshold' => 8.00,
            ],
            [
                'name'          => 'Caramel Syrup',
                'category'      => 'Syrup',
                'quantity'      => 6.00,
                'unit'          => 'L',
                'min_threshold' => 3.00,
            ],
            [
                'name'          => 'Vanilla Syrup',
                'category'      => 'Syrup',
                'quantity'      => 4.50,
                'unit'          => 'L',
                'min_threshold' => 3.00,
            ],
            [
                'name'          => 'Matcha Powder',
                'category'      => 'Powder',
                'quantity'      => 2.00,   // low
                'unit'          => 'kg',
                'min_threshold' => 4.00,
            ],
            [
                'name'          => 'Cocoa Powder',
                'category'      => 'Powder',
                'quantity'      => 3.50,
                'unit'          => 'kg',
                'min_threshold' => 2.00,
            ],
            [
                'name'          => 'Paper Cups (16oz)',
                'category'      => 'Supplies',
                'quantity'      => 450.00,
                'unit'          => 'pcs',
                'min_threshold' => 100.00,
            ],
            [
                'name'          => 'Paper Lids (16oz)',
                'category'      => 'Supplies',
                'quantity'      => 80.00,   // low
                'unit'          => 'pcs',
                'min_threshold' => 100.00,
            ],
            [
                'name'          => 'Stirrers',
                'category'      => 'Supplies',
                'quantity'      => 500.00,
                'unit'          => 'pcs',
                'min_threshold' => 150.00,
            ],
            [
                'name'          => 'Napkins',
                'category'      => 'Supplies',
                'quantity'      => 800.00,
                'unit'          => 'pcs',
                'min_threshold' => 200.00,
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create($item);
        }
    }
}