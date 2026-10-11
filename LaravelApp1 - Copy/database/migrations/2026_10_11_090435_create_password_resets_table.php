<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Password reset tokens.
     *
     * Since we have TWO user tables (admins + customers), we use a
     * `user_type` column to know which table the token belongs to.
     *
     * - user_type = 'customer' → token belongs to a customers row
     * - user_type = 'admin'    → token belongs to an admins row
     */
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id('reset_id');

            $table->string('email', 100);
            $table->string('token', 255);

            // Which account table this reset is for
            $table->enum('user_type', ['customer', 'admin'])->default('customer');

            $table->dateTime('expires_at');
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('email');
            $table->index('token');
            $table->index('user_type');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};