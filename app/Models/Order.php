<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'email',
        'total_amount_cents',
        'subtotal_cents',
        'tax_cents',
        'shipping_cents',
        'discount_cents',
        'payment_status',
        'fulfillment_status',
        'shipping_address',
        'billing_address',
        'order_notes',
        'admin_notes',
        'cancelled_at',
        'fulfilled_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'cancelled_at' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    /**
     * Boot the model and auto-generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    /**
     * Generate a unique order number
     */
    protected static function generateOrderNumber()
    {
        do {
            $orderNumber = 'FBO-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeUnfulfilled($query)
    {
        return $query->where('fulfillment_status', 'unfulfilled');
    }

    public function scopeFulfilled($query)
    {
        return $query->where('fulfillment_status', 'fulfilled');
    }

    /**
     * Order Management Methods
     */
    public function markAsFulfilled()
    {
        $this->update([
            'fulfillment_status' => 'fulfilled',
            'fulfilled_at' => now(),
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'fulfillment_status' => 'cancelled',
            'cancelled_at' => now(),
            'admin_notes' => $this->admin_notes . "\n\nCancelled: " . $reason,
        ]);
    }

    /**
     * Accessors
     */
    public function getFormattedTotalAttribute()
    {
        return '$' . number_format($this->total_amount_cents / 100, 2);
    }
}
