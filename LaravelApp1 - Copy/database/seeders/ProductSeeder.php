<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $coffee    = Category::where('name', 'Coffee')->first()->category_id;
        $nonCoffee = Category::where('name', 'Non-Coffee')->first()->category_id;
        $pastry    = Category::where('name', 'Pastry')->first()->category_id;

        $products = [
            // Coffee
            [
                'category_id'  => $coffee,
                'name'         => 'Issang Macchiato',
                'description'  => 'Our signature caramel macchiato with a double shot.',
                'price'        => 145.00,
                'image_url'    => 'images/products/macchiato.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'Spanish Latte',
                'description'  => 'Creamy, sweet, bold — a crowd favorite.',
                'price'        => 135.00,
                'image_url'    => 'images/products/spanish-latte.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'Americano',
                'description'  => 'Double shot of espresso, hot or iced.',
                'price'        => 110.00,
                'image_url'    => 'images/products/americano.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'Cappuccino',
                'description'  => 'Classic with velvety foam art.',
                'price'        => 125.00,
                'image_url'    => 'images/products/cappuccino.jpg',
                'is_available' => true,
            ],
            // Non-Coffee
            [
                'category_id'  => $nonCoffee,
                'name'         => 'Matcha Latte',
                'description'  => 'Stone-ground matcha with steamed milk.',
                'price'        => 140.00,
                'image_url'    => 'images/products/matcha-latte.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $nonCoffee,
                'name'         => 'Hot Chocolate',
                'description'  => 'Rich Belgian cocoa, topped with foam.',
                'price'        => 120.00,
                'image_url'    => 'images/products/hot-chocolate.jpg',
                'is_available' => true,
            ],
            // Pastry
            [
                'category_id'  => $pastry,
                'name'         => 'Butter Croissant',
                'description'  => 'Flaky, buttery, baked fresh daily.',
                'price'        => 85.00,
                'image_url'    => 'images/products/croissant.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $pastry,
                'name'         => 'Blueberry Muffin',
                'description'  => 'Loaded with wild blueberries.',
                'price'        => 95.00,
                'image_url'    => 'images/products/blueberry-muffin.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $pastry,
                'name'         => 'Chocolate Chip Cookie',
                'description'  => 'Soft-baked with dark chocolate chunks.',
                'price'        => 75.00,
                'image_url'    => 'images/products/cookie.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $pastry,
                'name'         => 'Banana Bread',
                'description'  => 'Moist, spiced, and sliced thick.',
                'price'        => 90.00,
                'image_url'    => 'images/products/banana-bread.jpg',
                'is_available' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}