<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inventory transactions — a log of every stock movement.
     *
     * Every time inventory changes (sale, restock, waste, adjustment),
     * a row is added here. This gives a full audit history.
     *
     * transaction_type:
     *   - 'add'      → restock / delivery
     *   - 'deduct'   → sold / used / wasted
     */
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id('transaction_id');

            // Which inventory item changed
            $table->unsignedBigInteger('inventory_id');

            $table->enum('transaction_type', ['add', 'deduct']);
            $table->decimal('quantity', 10, 2);

            // Where did this come from? e.g. 'sale', 'order', 'restock', 'adjustment'
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            // Who performed it? FK to admins (staff actions only)
            $table->unsignedBigInteger('performed_by')->nullable();

            $table->dateTime('transaction_date');
            $table->text('notes')->nullable();

            // Indexes
            $table->index('inventory_id');
            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index(['reference_type', 'reference_id']);

            // Foreign keys
            $table->foreign('inventory_id')
                  ->references('inventory_id')
                  ->on('inventory_items')
                  ->cascadeOnDelete();

            $table->foreign('performed_by')
                  ->references('admin_id')
                  ->on('admins')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};