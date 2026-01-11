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
            $table->json('party_addons')->nullable()->after('max_party_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('art_classes', function (Blueprint $table) {
            $table->dropColumn('party_addons');
        });
    }
};
