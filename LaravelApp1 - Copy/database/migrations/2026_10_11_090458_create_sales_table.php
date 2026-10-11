<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sales — walk-in transactions recorded by staff.
     *
     * Two kinds of sales:
     *   1. Walk-in        → no customer, no order link
     *   2. Pre-order pickup → linked to an order via reference_order_id
     *
     * Sales are always recorded by a staff member (staff_id is required).
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sale_id');

            // Who recorded this sale (required — staff action)
            $table->unsignedBigInteger('staff_id');

            // Optional customer (walk-in = NULL)
            $table->unsignedBigInteger('customer_id')->nullable();

            // Optional link to a pre-order
            $table->unsignedBigInteger('reference_order_id')->nullable();

            $table->dateTime('sale_date');
            $table->decimal('total_amount', 10, 2);

            $table->enum('payment_method', ['cash', 'gcash'])->default('cash');
            $table->enum('transaction_type', ['walk-in', 'pre-order'])->default('walk-in');

            $table->timestamps();

            // Foreign keys
            $table->foreign('staff_id')
                  ->references('admin_id')
                  ->on('admins')
                  ->restrictOnDelete();

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('customers')
                  ->nullOnDelete();

            $table->foreign('reference_order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->nullOnDelete();

            // Indexes for reporting
            $table->index('sale_date');
            $table->index('staff_id');
            $table->index('customer_id');
            $table->index('payment_method');
            $table->index('transaction_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};