<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inventory items — coffee beans, syrups, milk, cups, etc.
     *
     * Stock is stored here; changes are logged in inventory_transactions.
     * Low-stock alerts fire when quantity <= min_threshold.
     */
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id('inventory_id');

            $table->string('name', 100);
            $table->string('category', 50);

            // Quantity can be fractional (e.g. 2.5 kg)
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('unit', 20)->default('pcs');

            // Low-stock threshold
            $table->decimal('min_threshold', 10, 2)->default(0);

            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};