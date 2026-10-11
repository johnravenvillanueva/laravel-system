<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Notifications — system messages sent to users.
     *
     * Two targets:
     *   1. Customer    → customer_id is set, admin_id is NULL
     *   2. Admin/Staff → admin_id is set, customer_id is NULL
     *
     * Types:
     *   - order_update  → status change notification for a customer
     *   - new_order     → alert to staff that an order came in
     *   - low_stock     → alert to admin/staff that inventory is running low
     *   - announcement  → admin-authored broadcast
     *   - promo         → promotional message
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');

            // Recipients (one of these will be set)
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();

            // Optional links
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('announcement_id')->nullable();

            $table->string('title', 100);
            $table->text('message');

            $table->enum('type', [
                'order_update',
                'new_order',
                'low_stock',
                'announcement',
                'promo',
            ])->default('order_update');

            $table->boolean('is_read')->default(false);

            $table->timestamps();

            // Foreign keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('customers')
                  ->cascadeOnDelete();

            $table->foreign('admin_id')
                  ->references('admin_id')
                  ->on('admins')
                  ->cascadeOnDelete();

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->nullOnDelete();

            $table->foreign('announcement_id')
                  ->references('announcement_id')
                  ->on('announcements')
                  ->nullOnDelete();

            // Indexes for the notification list
            $table->index(['customer_id', 'is_read']);
            $table->index(['admin_id', 'is_read']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};