<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $coffee = Category::where('name', 'Coffee')->first()->category_id;
        $pastry = Category::where('name', 'Pastry')->first()->category_id;

        $products = [
            // --- COFFEE / DRINKS ---
            [
                'category_id'  => $coffee,
                'name'         => 'Iced Caramel Macchiatos',
                'description'  => 'Our signature caramel macchiato with a delicious taste.',
                'price'        => 49.00,
                'image_url'    => 'images/products/iced-caramel-macchiato.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'Icy Choco',
                'description'  => 'Creamy, sweet, chocolatey flavored iced coffee.',
                'price'        => 49.00,
                'image_url'    => 'images/products/icy-choco.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'Strawberry Bliss',
                'description'  => 'Refreshing coffee with a unique strawberry taste.',
                'price'        => 49.00,
                'image_url'    => 'images/products/strawberry-bliss.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'Machatos',
                'description'  => 'Taste of nature in a cup.',
                'price'        => 49.00,
                'image_url'    => 'images/products/machatos.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'MatchaBerry',
                'description'  => 'Nature in a cup with a hint of strawberry.',
                'price'        => 49.00,
                'image_url'    => 'images/products/matchaberry.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'ChocoBerry',
                'description'  => 'Chocolate and strawberry blended to perfection.',
                'price'        => 49.00,
                'image_url'    => 'images/products/chocoberry.png',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'Milky Ube',
                'description'  => 'Creamy purple yam flavored drink.',
                'price'        => 49.00,
                'image_url'    => 'images/products/milky-ube.jpg',
                'is_available' => true,
            ],
            [
                'category_id'  => $coffee,
                'name'         => 'Mango Bliss',
                'description'  => 'Sweet and refreshing mango flavored coffee.',
                'price'        => 49.00,
                'image_url'    => 'images/products/mango-bliss.png',
                'is_available' => true,
            ],

            // --- PASTRIES / COOKIES ---
            [
                'category_id'  => $pastry,
                'name'         => 'Chocolate Cookie',
                'description'  => 'Classic chewy chocolate chip cookie.',
                'price'        => 49.00,
                'image_url'    => 'images/products/chocolate-cookie.png',
                'is_available' => true,
            ],
            [
                'category_id'  => $pastry,
                'name'         => 'Rocky Road Cookie',
                'description'  => 'Loaded with marshmallows, nuts, and chocolate.',
                'price'        => 49.00,
                'image_url'    => 'images/products/rocky-road-cookie.png',
                'is_available' => true,
            ],
            [
                'category_id'  => $pastry,
                'name'         => 'Red Velvet Cookie',
                'description'  => 'I love red velvet!',
                'price'        => 49.00,
                'image_url'    => 'images/products/red-velvet-cookie.png',
                'is_available' => true,
            ],
            [
                'category_id'  => $pastry,
                'name'         => 'Strawberry Cookie',
                'description'  => 'Strawberry-flavored Cookie.',
                'price'        => 49.00,
                'image_url'    => 'images/products/strawberry-cookie.png',
                'is_available' => true,
            ],
            [
                'category_id'  => $pastry,
                'name'         => 'Matcha Cookie',
                'description'  => 'Taste of nature in a cookie.',
                'price'        => 49.00,
                'image_url'    => 'images/products/matcha-cookie.png',
                'is_available' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}