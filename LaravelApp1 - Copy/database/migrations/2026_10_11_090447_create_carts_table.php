<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Carts — one active cart per customer.
     *
     * Guests do NOT get a DB cart. Their cart lives in session only.
     * When a logged-in customer adds items, a cart is created and
     * cart_items reference it.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id('cart_id');

            // One cart per customer
            $table->unsignedBigInteger('customer_id');

            $table->timestamps();

            // Foreign key
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('customers')
                  ->cascadeOnDelete();

            // Unique — a customer can only have one active cart
            $table->unique('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};