<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('art_classes', function (Blueprint $table) {
            $table->json('ticket_types')->nullable()->after('price_cents');
        });
    }

    public function down(): void
    {
        Schema::table('art_classes', function (Blueprint $table) {
            $table->dropColumn('ticket_types');
        });
    }
};
