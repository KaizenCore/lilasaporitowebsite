<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'order_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_customer_id',
        'amount_cents',
        'currency',
        'payment_method',
        'status',
        'stripe_fee_cents',
        'net_amount_cents',
        'failure_reason',
        'refund_amount_cents',
        'refunded_at',
        'metadata',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'stripe_fee_cents' => 'integer',
        'net_amount_cents' => 'integer',
        'refund_amount_cents' => 'integer',
        'refunded_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Helper methods
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

    public function calculateStripeFee()
    {
        // Stripe fee: 2.9% + $0.30
        $percentageFee = (int) ($this->amount_cents * 0.029);
        $fixedFee = 30; // 30 cents
        $totalFee = $percentageFee + $fixedFee;

        $this->stripe_fee_cents = $totalFee;
        $this->net_amount_cents = $this->amount_cents - $totalFee;
    }

    public function markAsSucceeded()
    {
        $this->update(['status' => 'succeeded']);

        // Update booking if this payment is for a booking
        if ($this->booking_id && $this->booking) {
            $this->booking->update(['payment_status' => 'completed']);
        }

        // Update order if this payment is for an order
        if ($this->order_id && $this->order) {
            $this->order->update(['payment_status' => 'completed']);
        }
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);

        // Update booking if this payment is for a booking
        if ($this->booking_id && $this->booking) {
            $this->booking->update(['payment_status' => 'failed']);
        }

        // Update order if this payment is for an order
        if ($this->order_id && $this->order) {
            $this->order->update(['payment_status' => 'failed']);
        }
    }

    public function scopeSucceeded($query)
    {
        return $query->where('status', 'succeeded');
    }
}
