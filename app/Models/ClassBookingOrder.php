<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassBookingOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'email',
        'total_amount_cents',
        'subtotal_cents',
        'discount_cents',
        'discount_code',
        'payment_status',
        'order_notes',
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
     * Generate a unique order number (FBC = FrizzBoss Class)
     */
    protected static function generateOrderNumber()
    {
        do {
            $orderNumber = 'FBC-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
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

    public function bookings()
    {
        return $this->hasMany(Booking::class);
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

    /**
     * Accessors
     */
    public function getFormattedTotalAttribute()
    {
        return '$' . number_format($this->total_amount_cents / 100, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        return '$' . number_format($this->subtotal_cents / 100, 2);
    }

    public function getFormattedDiscountAttribute()
    {
        return '$' . number_format($this->discount_cents / 100, 2);
    }

    public function getBookingCountAttribute()
    {
        return $this->bookings()->count();
    }
}
