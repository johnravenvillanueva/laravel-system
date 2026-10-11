<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Product categories.
     *
     * Examples: Coffee, Non-Coffee, Pastry
     * Products belong to exactly one category.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');

            $table->string('name', 50)->unique();
            $table->text('description')->nullable();

            $table->timestamps();

            // Index for sorting/searching by name
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};