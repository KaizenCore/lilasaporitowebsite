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
        Schema::table('payments', function (Blueprint $table) {
            // Make booking_id nullable (was required before)
            $table->foreignId('booking_id')->nullable()->change();

            // Add order_id foreign key
            $table->foreignId('order_id')->nullable()->after('booking_id')->constrained('orders')->cascadeOnDelete();

            // Add index for order_id
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove order_id column
            $table->dropForeign(['order_id']);
            $table->dropIndex(['order_id']);
            $table->dropColumn('order_id');

            // Make booking_id required again
            $table->foreignId('booking_id')->nullable(false)->change();
        });
    }
};
