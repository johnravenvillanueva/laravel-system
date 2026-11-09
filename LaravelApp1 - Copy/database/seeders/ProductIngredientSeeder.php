<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductIngredient;
use Illuminate\Database\Seeder;

class ProductIngredientSeeder extends Seeder
{
    /**
     * Starter recipes for the seeded products (admins can edit them
     * from Products → Edit).
     */
    public function run(): void
    {
        $inventory = InventoryItem::pluck('inventory_id', 'name');

        foreach (Product::all() as $product) {
            foreach (Product::legacyRecipe($product->name) as $itemName => $qty) {
                if (! isset($inventory[$itemName])) continue;

                ProductIngredient::updateOrCreate(
                    ['product_id' => $product->product_id, 'inventory_id' => $inventory[$itemName]],
                    ['quantity' => $qty]
                );
            }
        }
    }
}
