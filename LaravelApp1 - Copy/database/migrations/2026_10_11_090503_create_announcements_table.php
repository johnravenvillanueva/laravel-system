<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Announcements — admin-authored messages broadcast to users.
     *
     * target_audience:
     *   - 'all'       → customers + staff
     *   - 'customers' → customers only
     *   - 'staff'     → admins and staff only
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('announcement_id');

            // Who wrote it (required — admin action)
            $table->unsignedBigInteger('created_by');

            $table->string('title', 100);
            $table->text('content');

            $table->enum('target_audience', ['all', 'customers', 'staff'])
                  ->default('all');

            $table->boolean('is_active')->default(true);

            // Optional expiry — after this date, the announcement is hidden
            $table->dateTime('expires_at')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('created_by')
                  ->references('admin_id')
                  ->on('admins')
                  ->restrictOnDelete();

            // Indexes
            $table->index('is_active');
            $table->index('target_audience');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};