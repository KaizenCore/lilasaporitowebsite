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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->string('email_type', 50);
            $table->string('recipient_email');
            $table->string('subject');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('booking_id');
            $table->index('email_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
