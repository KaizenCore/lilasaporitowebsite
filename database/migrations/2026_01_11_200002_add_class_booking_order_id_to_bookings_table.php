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
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('class_booking_order_id')
                ->nullable()
                ->after('art_class_id')
                ->constrained('class_booking_orders')
                ->nullOnDelete();

            $table->index('class_booking_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['class_booking_order_id']);
            $table->dropIndex(['class_booking_order_id']);
            $table->dropColumn('class_booking_order_id');
        });
    }
};
