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
        Schema::table('art_classes', function (Blueprint $table) {
            $table->boolean('is_party_event')->default(false)->after('status');
            $table->integer('small_party_price_cents')->nullable()->after('is_party_event');
            $table->integer('small_party_size')->nullable()->after('small_party_price_cents');
            $table->integer('large_party_price_cents')->nullable()->after('small_party_size');
            $table->integer('large_party_size')->nullable()->after('large_party_price_cents');
            $table->integer('additional_guest_price_cents')->nullable()->after('large_party_size');
            $table->integer('max_party_size')->nullable()->after('additional_guest_price_cents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('art_classes', function (Blueprint $table) {
            $table->dropColumn([
                'is_party_event',
                'small_party_price_cents',
                'small_party_size',
                'large_party_price_cents',
                'large_party_size',
                'additional_guest_price_cents',
                'max_party_size',
            ]);
        });
    }
};
