<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admins + Staff live in this table.
     * - role = 'admin'  → full access
     * - role = 'staff'  → limited access (sales, orders, inventory)
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id');

            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 15)->nullable();
            $table->string('avatar', 255)->nullable();

            $table->enum('role', ['admin', 'staff'])->default('staff');
            $table->boolean('is_active')->default(true);

            $table->dateTime('last_login')->nullable();

            // Self-referencing: who created this account?
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('role');
            $table->index('is_active');
            $table->index('created_by');
        });

        // Add self-FK after table exists
        Schema::table('admins', function (Blueprint $table) {
            $table->foreign('created_by')
                  ->references('admin_id')
                  ->on('admins')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};