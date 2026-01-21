<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'party_booking_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_customer_id',
        'amount_cents',
        'currency',
        'payment_type',
        'payment_method',
        'status',
        'stripe_fee_cents',
        'net_amount_cents',
        'failure_reason',
        'refund_amount_cents',
        'refunded_at',
        'is_test',
        'metadata',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'stripe_fee_cents' => 'integer',
        'net_amount_cents' => 'integer',
        'refund_amount_cents' => 'integer',
        'refunded_at' => 'datetime',
        'is_test' => 'boolean',
        'metadata' => 'array',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_FINAL = 'final';
    const TYPE_FULL = 'full';

    // Relationships
    public function partyBooking()
    {
        return $this->belongsTo(PartyBooking::class);
    }

    // Helpers
    /**
     * Calculate and store Stripe fee (2.9% + $0.30).
     */
    public function calculateStripeFee(): void
    {
        $percentageFee = (int) round($this->amount_cents * 0.029);
        $fixedFee = 30; // 30 cents
        $totalFee = $percentageFee + $fixedFee;

        $this->stripe_fee_cents = $totalFee;
        $this->net_amount_cents = $this->amount_cents - $totalFee;
        $this->save();
    }

    /**
     * Mark payment as succeeded and update booking.
     */
    public function markAsSucceeded(): void
    {
        $this->status = self::STATUS_SUCCEEDED;
        $this->save();

        $this->calculateStripeFee();

        // Update booking payment totals
        $booking = $this->partyBooking;
        $booking->total_paid_cents += $this->amount_cents;

        if ($this->payment_type === self::TYPE_DEPOSIT) {
            $booking->deposit_paid_cents = $this->amount_cents;
            $booking->deposit_paid_at = now();
            $booking->payment_status = PartyBooking::PAYMENT_DEPOSIT_PAID;
            $booking->status = PartyBooking::STATUS_DEPOSIT_PAID;
        }

        if ($booking->total_paid_cents >= $booking->quoted_total_cents) {
            $booking->payment_status = PartyBooking::PAYMENT_PAID;
            $booking->status = PartyBooking::STATUS_CONFIRMED;
        }

        $booking->save();
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->status = self::STATUS_FAILED;
        $this->failure_reason = $reason;
        $this->save();
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount_cents / 100, 2);
    }

    public function getFormattedNetAmountAttribute()
    {
        return '$' . number_format(($this->net_amount_cents ?? 0) / 100, 2);
    }

    public function getFormattedStripeFeeAttribute()
    {
        return '$' . number_format(($this->stripe_fee_cents ?? 0) / 100, 2);
    }

    public function getPaymentTypeDisplayAttribute()
    {
        return match ($this->payment_type) {
            self::TYPE_DEPOSIT => 'Deposit',
            self::TYPE_FINAL => 'Final Payment',
            self::TYPE_FULL => 'Full Payment',
            default => ucfirst($this->payment_type),
        };
    }

    public function getStatusBadgeColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_PROCESSING => 'blue',
            self::STATUS_SUCCEEDED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_REFUNDED => 'gray',
            default => 'gray',
        };
    }

    // Scopes
    public function scopeSucceeded($query)
    {
        return $query->where('status', self::STATUS_SUCCEEDED);
    }

    public function scopeLive($query)
    {
        return $query->where('is_test', false);
    }

    public function scopeTest($query)
    {
        return $query->where('is_test', true);
    }
}
