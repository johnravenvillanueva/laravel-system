<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Product ingredients (recipe) — which inventory items a product
     * deducts, and how much of each per 1 unit sold.
     *
     * Existing products are back-filled with the rules that used to be
     * hard-coded in Product::deductInventory(), so current behaviour is
     * unchanged but is now visible/editable in the admin product forms.
     */
    public function up(): void
    {
        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('inventory_id');

            // Amount deducted per 1 product sold, in the inventory item's unit
            $table->decimal('quantity', 10, 2);

            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete();
            $table->foreign('inventory_id')->references('inventory_id')->on('inventory_items')->cascadeOnDelete();
            $table->unique(['product_id', 'inventory_id']);
        });

        // Back-fill existing products from the old name-based rules
        $inventory = DB::table('inventory_items')->pluck('inventory_id', 'name');

        foreach (DB::table('products')->get() as $product) {
            foreach (Product::legacyRecipe($product->name) as $itemName => $qty) {
                if (! isset($inventory[$itemName])) {
                    continue;
                }
                DB::table('product_ingredients')->insert([
                    'product_id'   => $product->product_id,
                    'inventory_id' => $inventory[$itemName],
                    'quantity'     => $qty,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
    }
};
