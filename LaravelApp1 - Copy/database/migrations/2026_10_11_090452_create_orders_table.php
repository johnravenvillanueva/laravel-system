<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Orders — pre-orders placed by customers OR guests.
     *
     * Two kinds of orders:
     *   1. Registered customer → customer_id is set, guest_* are NULL
     *   2. Guest checkout      → customer_id is NULL, guest_* are filled
     *
     * The app enforces: EXACTLY ONE of (customer_id, guest_name+guest_phone)
     * must be present. MySQL can't enforce that with simple constraints.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');

            // Human-readable order code, e.g. IM-00042
            $table->string('reference_code', 20)->unique();

            // Either a registered customer OR a guest (nullable)
            $table->unsignedBigInteger('customer_id')->nullable();

            // Guest contact info (only used when customer_id is NULL)
            $table->string('guest_name', 100)->nullable();
            $table->string('guest_phone', 15)->nullable();
            $table->string('guest_email', 100)->nullable();

            // Schedule + status
            $table->dateTime('order_date');
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'completed',
                'cancelled',
            ])->default('pending');

            // Money
            $table->decimal('total_amount', 10, 2);

            // Payment
            $table->enum('payment_method', ['cash', 'gcash'])->default('cash');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('customers')
                  ->nullOnDelete();

            // Indexes for admin order list + reports
            $table->index('status');
            $table->index('pickup_date');
            $table->index('order_date');
            $table->index('payment_status');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};