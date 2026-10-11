<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Customers — both registered users and guest checkouts.
     *
     * - is_guest = false → normal self-registered customer
     * - is_guest = true  → created at guest checkout, may not have password
     *
     * email and password_hash are nullable to support guest rows.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');

            $table->string('email', 100)->nullable()->unique();
            $table->string('password_hash', 255)->nullable();

            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 15);

            $table->text('address')->nullable();
            $table->string('avatar', 255)->nullable();

            $table->boolean('is_guest')->default(false);
            $table->boolean('is_active')->default(true);

            $table->dateTime('last_login')->nullable();

            $table->timestamps();

            // Indexes for filtering / lookups
            $table->index('phone');
            $table->index('is_guest');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};