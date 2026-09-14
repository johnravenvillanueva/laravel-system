<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $items = [
            [
                'name' => 'Coffee Beans - Arabica',
                'category' => 'Coffee',
                'quantity' => '5.00',
                'unit' => 'kg',
                'min_threshold' => '1.00',
                'status' => 'IN STOCK',
            ],
            [
                'name' => 'Coffee Beans - Robusta',
                'category' => 'Coffee',
                'quantity' => '3.00',
                'unit' => 'kg',
                'min_threshold' => '1.00',
                'status' => 'IN STOCK',
            ],
            [
                'name' => 'Butter',
                'category' => 'Dairy',
                'quantity' => '2.00',
                'unit' => 'kg',
                'min_threshold' => '0.50',
                'status' => 'IN STOCK',
            ],
            [
                'name' => 'Cream Cheese',
                'category' => 'Dairy',
                'quantity' => '3.00',
                'unit' => 'kg',
                'min_threshold' => '0.50',
                'status' => 'IN STOCK',
            ],
            [
                'name' => 'Milk - Fresh',
                'category' => 'Dairy',
                'quantity' => '10.00',
                'unit' => 'liters',
                'min_threshold' => '2.00',
                'status' => 'IN STOCK',
            ],
            [
                'name' => 'Chicken Breast',
                'category' => 'Food',
                'quantity' => '5.00',
                'unit' => 'kg',
                'min_threshold' => '1.00',
                'status' => 'IN STOCK',
            ],
            [
                'name' => 'Rice',
                'category' => 'Food',
                'quantity' => '10.00',
                'unit' => 'kg',
                'min_threshold' => '2.00',
                'status' => 'IN STOCK',
            ],
        ];

        return view('home', ['items' => $items]);
    }
}