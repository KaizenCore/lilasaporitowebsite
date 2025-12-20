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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_intent_id')->unique()->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->integer('amount_cents');
            $table->string('currency', 3)->default('usd');
            $table->string('payment_method', 50)->nullable();
            $table->enum('status', ['pending', 'processing', 'succeeded', 'failed', 'refunded'])->default('pending');
            $table->integer('stripe_fee_cents')->nullable();
            $table->integer('net_amount_cents')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('refund_amount_cents')->default(0)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('booking_id');
            $table->index('stripe_customer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
