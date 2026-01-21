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
        Schema::create('party_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique(); // FBP-XXXX
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Status workflow
            $table->string('status')->default('inquiry'); // inquiry, quoted, accepted, deposit_paid, confirmed, completed, cancelled, declined

            // Event details
            $table->date('preferred_date');
            $table->time('preferred_time')->nullable();
            $table->date('alternate_date')->nullable();
            $table->time('alternate_time')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->time('confirmed_time')->nullable();

            // Location
            $table->string('location_type'); // lila_hosts, customer_location
            $table->text('customer_address')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_state')->nullable();
            $table->string('customer_zip')->nullable();

            // Party details
            $table->integer('guest_count');
            $table->text('event_details')->nullable();
            $table->string('event_type')->nullable(); // birthday, corporate, bridal_shower, etc.
            $table->string('honoree_name')->nullable();
            $table->integer('honoree_age')->nullable();

            // Painting selection
            $table->foreignId('party_painting_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('wants_custom_painting')->default(false);
            $table->text('custom_painting_description')->nullable();

            // Pricing
            $table->foreignId('party_pricing_config_id')->nullable()->constrained()->nullOnDelete();
            $table->json('selected_addon_ids')->nullable(); // Array of party_addon IDs
            $table->integer('quoted_subtotal_cents')->nullable();
            $table->integer('quoted_addons_cents')->nullable();
            $table->integer('quoted_venue_fee_cents')->nullable();
            $table->integer('quoted_custom_painting_fee_cents')->nullable();
            $table->integer('quoted_adjustment_cents')->nullable(); // Manual adjustment (+/-)
            $table->integer('quoted_total_cents')->nullable();
            $table->text('quote_notes')->nullable();
            $table->timestamp('quote_sent_at')->nullable();
            $table->timestamp('quote_expires_at')->nullable();

            // Payment tracking
            $table->integer('deposit_required_cents')->nullable();
            $table->integer('deposit_paid_cents')->default(0);
            $table->timestamp('deposit_paid_at')->nullable();
            $table->integer('total_paid_cents')->default(0);
            $table->string('payment_status')->default('unpaid'); // unpaid, deposit_paid, paid

            // Contact info
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();

            // Admin fields
            $table->text('admin_notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('preferred_date');
            $table->index('payment_status');
            $table->index(['status', 'preferred_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('party_bookings');
    }
};
