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
        Schema::create('party_availability_slots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('available'); // available, booked, blocked
            $table->string('block_reason')->nullable(); // For blackout dates
            $table->foreignId('party_booking_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['date', 'start_time']); // Prevent overlapping slots
            $table->index('date');
            $table->index('status');
            $table->index(['date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('party_availability_slots');
    }
};
