<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_pickup_slots', function (Blueprint $table) {
            $table->id();
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->timestamps();
            $table->unique(['pickup_date', 'pickup_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_pickup_slots');
    }
};
