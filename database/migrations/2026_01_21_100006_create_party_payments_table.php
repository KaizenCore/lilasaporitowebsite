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
        Schema::create('party_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_booking_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_charge_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->integer('amount_cents');
            $table->string('currency', 3)->default('usd');
            $table->string('payment_type'); // deposit, final, full
            $table->string('payment_method')->nullable();
            $table->string('status')->default('pending'); // pending, processing, succeeded, failed, refunded
            $table->integer('stripe_fee_cents')->nullable();
            $table->integer('net_amount_cents')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('refund_amount_cents')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->boolean('is_test')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('payment_type');
            $table->index('stripe_payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('party_payments');
    }
};
