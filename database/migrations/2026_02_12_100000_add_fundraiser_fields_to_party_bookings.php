<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('party_bookings', function (Blueprint $table) {
            $table->string('fundraiser_org_name')->nullable()->after('honoree_age');
            $table->string('fundraiser_cause')->nullable()->after('fundraiser_org_name');
            $table->string('fundraiser_type')->nullable()->after('fundraiser_cause');
        });
    }

    public function down(): void
    {
        Schema::table('party_bookings', function (Blueprint $table) {
            $table->dropColumn(['fundraiser_org_name', 'fundraiser_cause', 'fundraiser_type']);
        });
    }
};
