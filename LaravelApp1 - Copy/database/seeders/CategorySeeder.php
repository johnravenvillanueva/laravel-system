<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee',     'description' => 'Espresso-based hot and iced drinks'],
            ['name' => 'Non-Coffee', 'description' => 'Matcha, chocolate, and fruit-based drinks'],
            ['name' => 'Pastry',     'description' => 'Freshly baked breads and sweets'],
        ];

        foreach ($categories as $c) {
            Category::create($c);
        }
    }
}