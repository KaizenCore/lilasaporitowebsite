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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('art_class_id')->constrained()->onDelete('cascade');
            $table->string('ticket_code', 20)->unique();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('attendance_status', ['booked', 'attended', 'no_show', 'cancelled'])->default('booked');
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('booking_notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('art_class_id');
            $table->index('payment_status');
            $table->index(['art_class_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
