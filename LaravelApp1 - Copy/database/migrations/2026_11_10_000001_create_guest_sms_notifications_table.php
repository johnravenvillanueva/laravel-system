<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_sms_notifications', function (Blueprint $table) {
            $table->id('guest_sms_notification_id');
            $table->unsignedBigInteger('order_id');
            $table->text('message');
            $table->string('delivery_status', 20)->default('simulated');
            $table->timestamps();

            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders')
                ->cascadeOnDelete();
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_sms_notifications');
    }
};
