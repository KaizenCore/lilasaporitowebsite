<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('is_test')->default(false)->after('status');
        });

        // Mark existing demo/test payments as test
        DB::table('payments')
            ->where('stripe_payment_intent_id', 'like', 'pi_demo_%')
            ->orWhere('stripe_payment_intent_id', 'like', 'pi_test_%')
            ->update(['is_test' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('is_test');
        });
    }
};
