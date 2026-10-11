<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit logs — a permanent record of every important action.
     *
     * Three user types can perform actions:
     *   - admin_id    → admin
     *   - staff_id    → staff (also stored in admins table)
     *   - customer_id → customer
     *
     * At least one of these should be set. Guests have none.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('log_id');

            // Who performed the action (nullable — one will be set)
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            // What they did
            $table->string('action', 50);

            // What table + record was affected
            $table->string('table_name', 50)->nullable();
            $table->unsignedBigInteger('record_id')->nullable();

            // Before / after snapshots
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();

            // Where they did it from
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            // When
            $table->dateTime('timestamp')->useCurrent();

            // Indexes for filtering logs
            $table->index('admin_id');
            $table->index('staff_id');
            $table->index('customer_id');
            $table->index('action');
            $table->index(['table_name', 'record_id']);
            $table->index('timestamp');

            // Foreign keys — nullable, don't cascade-delete history
            $table->foreign('admin_id')
                  ->references('admin_id')
                  ->on('admins')
                  ->nullOnDelete();

            $table->foreign('staff_id')
                  ->references('admin_id')
                  ->on('admins')
                  ->nullOnDelete();

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('customers')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};