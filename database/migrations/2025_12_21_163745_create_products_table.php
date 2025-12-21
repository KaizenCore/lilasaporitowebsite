<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('short_description', 500)->nullable();
            $table->integer('price_cents');
            $table->integer('compare_at_price_cents')->nullable();
            $table->enum('product_type', ['physical', 'digital', 'class_package']);
            $table->string('image_path')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('digital_file_path')->nullable();
            $table->integer('stock_quantity')->nullable();
            $table->string('sku', 50)->unique()->nullable();
            $table->integer('weight_grams')->nullable();
            $table->boolean('requires_shipping')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('product_type');
            $table->index('status');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
