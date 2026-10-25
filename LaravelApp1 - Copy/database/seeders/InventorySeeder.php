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
                'name'          => 'Coffee Beans (Arabica)',
                'category'      => 'Coffee Bean',
                'quantity'      => 15.00,
                'unit'          => 'kg',
                'min_threshold' => 5.00,
            ],
            [
                'name'          => 'Matcha Powder',
                'category'      => 'Powder',
                'quantity'      => 3.00,   // low
                'unit'          => 'kg',
                'min_threshold' => 4.00,
            ],
            [
                'name'          => 'Ube Extract',
                'category'      => 'Syrup',
                'quantity'      => 2.00,   // low
                'unit'          => 'L',
                'min_threshold' => 3.00,
            ],
            [
                'name'          => 'Strawberry Syrup',
                'category'      => 'Syrup',
                'quantity'      => 6.00,
                'unit'          => 'L',
                'min_threshold' => 2.00,
            ],
            [
                'name'          => 'Chocolate Syrup',
                'category'      => 'Syrup',
                'quantity'      => 8.00,
                'unit'          => 'L',
                'min_threshold' => 3.00,
            ],
            [
                'name'          => 'Fresh Milk',
                'category'      => 'Dairy',
                'quantity'      => 25.00,
                'unit'          => 'L',
                'min_threshold' => 10.00,
            ],
            [
                'name'          => 'All-Purpose Flour',
                'category'      => 'Baking',
                'quantity'      => 20.00,
                'unit'          => 'kg',
                'min_threshold' => 5.00,
            ],
            [
                'name'          => 'Plastic Cups (16oz)',
                'category'      => 'Supplies',
                'quantity'      => 450.00,
                'unit'          => 'pcs',
                'min_threshold' => 100.00,
            ],
            [
                'name'          => 'Paper Lids',
                'category'      => 'Supplies',
                'quantity'      => 80.00,   // low
                'unit'          => 'pcs',
                'min_threshold' => 100.00,
            ],
            [
                'name'          => 'Straws',
                'category'      => 'Supplies',
                'quantity'      => 500.00,
                'unit'          => 'pcs',
                'min_threshold' => 150.00,
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create($item);
        }
    }
}