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
        Schema::create('party_pricing_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Standard Kids Party", "Corporate Event"
            $table->string('pricing_type'); // flat_per_person, tiered, custom_quote
            $table->integer('base_price_cents')->nullable(); // For flat per-person pricing
            $table->integer('minimum_guests')->default(4);
            $table->integer('maximum_guests')->nullable();
            $table->json('tier_pricing')->nullable(); // For tiered: [{min: 4, max: 6, price_cents: 3500}, ...]
            $table->integer('lila_venue_fee_cents')->nullable(); // Additional fee when Lila hosts
            $table->integer('lila_venue_per_person_cents')->nullable(); // Alternative: per-person rate for Lila-hosted
            $table->integer('lila_venue_max_capacity')->default(8);
            $table->integer('custom_painting_fee_cents')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('is_active');
            $table->index('pricing_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('party_pricing_configs');
    }
};
