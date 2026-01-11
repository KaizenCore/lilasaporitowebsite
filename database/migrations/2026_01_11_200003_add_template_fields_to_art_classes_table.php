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
            $table->unsignedBigInteger('template_source_id')->nullable()->after('created_by');
            $table->string('series_name')->nullable()->after('template_source_id');

            $table->index('template_source_id');
            $table->index('series_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('art_classes', function (Blueprint $table) {
            $table->dropIndex(['template_source_id']);
            $table->dropIndex(['series_name']);
            $table->dropColumn(['template_source_id', 'series_name']);
        });
    }
};
