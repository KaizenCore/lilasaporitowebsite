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
        Schema::create('art_classes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('short_description', 500)->nullable();
            $table->text('materials_included')->nullable();
            $table->string('image_path')->nullable();
            $table->dateTime('class_date');
            $table->integer('duration_minutes')->default(120);
            $table->integer('price_cents');
            $table->integer('capacity');
            $table->string('location');
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index('class_date');
            $table->index('status');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('art_classes');
    }
};
