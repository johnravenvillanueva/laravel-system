<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Products — coffee, non-coffee, and pastries.
     *
     * NOTE: The ERD originally had a `type ENUM('coffee','pastry')` column.
     * That's redundant — the category already tells us what kind of product
     * it is. We use category_id only.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');

            // FK to categories
            $table->unsignedBigInteger('category_id');

            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url', 255)->nullable();
            $table->boolean('is_available')->default(true);

            $table->timestamps();

            // Foreign key
            $table->foreign('category_id')
                  ->references('category_id')
                  ->on('categories')
                  ->restrictOnDelete();

            // Indexes
            $table->index('category_id');
            $table->index('is_available');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};