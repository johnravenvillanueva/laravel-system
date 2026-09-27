<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Pork Adobo', 'category' => 'lunch', 'description' => 'Slow-braised pork, garlic rice', 'price' => 99, 'stock' => 18, 'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=640&q=80'],
            ['name' => 'Chicken Adobo', 'category' => 'lunch', 'description' => 'Classic adobo with steamed rice', 'price' => 99, 'stock' => 15, 'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=640&q=80'],
            ['name' => 'Strawberry Bliss', 'category' => 'coffee', 'description' => 'Strawberry milk with espresso', 'price' => 49, 'stock' => 24, 'image' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=640&q=80'],
            ['name' => 'Mango Bliss', 'category' => 'coffee', 'description' => 'Mango cream, espresso, ice', 'price' => 49, 'stock' => 20, 'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=640&q=80'],
            ['name' => 'Icy Choco', 'category' => 'coffee', 'description' => 'Iced cocoa with a creamy finish', 'price' => 49, 'stock' => 21, 'image' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?auto=format&fit=crop&w=640&q=80'],
            ['name' => 'Vanilla Latte', 'category' => 'coffee', 'description' => 'Espresso, milk, house vanilla', 'price' => 49, 'stock' => 22, 'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=640&q=80'],
            ['name' => 'Caramel Macchiato', 'category' => 'coffee', 'description' => 'Espresso layered with caramel', 'price' => 59, 'stock' => 16, 'image' => 'https://images.unsplash.com/photo-1461988320302-91bde64fc8e4?auto=format&fit=crop&w=640&q=80'],
            ['name' => 'Strawberry Cookie', 'category' => 'pastry', 'description' => 'Soft-baked cookie with strawberry', 'price' => 25, 'stock' => 5, 'image' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=640&q=80'],
            ['name' => 'Butter Croissant', 'category' => 'pastry', 'description' => 'Flaky, laminated pastry', 'price' => 49, 'stock' => 8, 'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=640&q=80'],
            ['name' => 'Mango Bites', 'category' => 'pastry', 'description' => 'Mango-filled bite-size pastries', 'price' => 49, 'stock' => 12, 'image' => 'https://images.unsplash.com/photo-1509365465985-25d11c17e812?auto=format&fit=crop&w=640&q=80'],
        ];

        foreach ($products as $product) {
            $record = Product::firstOrCreate(
                ['name' => $product['name']],
                $product + ['is_active' => true]
            );

            if (! $record->image && $product['image']) {
                $record->update(['image' => $product['image']]);
            }
        }
    }
}