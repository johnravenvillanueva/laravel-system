<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cart items — one row per product inside a cart.
     *
     * A customer adds "2x Spanish Latte" → one row with quantity = 2.
     * If they add a different product, a second row appears.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('cart_item_id');

            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');

            $table->integer('quantity')->default(1);
            $table->text('special_instructions')->nullable();

            $table->timestamp('added_at')->useCurrent();

            // Foreign keys
            $table->foreign('cart_id')
                  ->references('cart_id')
                  ->on('carts')
                  ->cascadeOnDelete();

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->cascadeOnDelete();

            // Indexes
            $table->index('cart_id');
            $table->index('product_id');

            // Prevent duplicate rows for the same product in the same cart
            $table->unique(['cart_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};