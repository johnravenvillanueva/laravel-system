<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sale items — the products inside a sale.
     *
     * Mirrors order_items, but for sales (walk-in or pre-order pickups).
     * Prices are snapshotted at sale time.
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id('sale_item_id');

            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('product_id');

            $table->integer('quantity');

            // Snapshot prices
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);

            // Foreign keys
            $table->foreign('sale_id')
                  ->references('sale_id')
                  ->on('sales')
                  ->cascadeOnDelete();

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->restrictOnDelete();

            // Indexes
            $table->index('sale_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};