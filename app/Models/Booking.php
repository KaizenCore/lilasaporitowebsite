<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'art_class_id',
        'ticket_code',
        'payment_status',
        'attendance_status',
        'checked_in_at',
        'cancelled_at',
        'cancellation_reason',
        'booking_notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->ticket_code)) {
                $booking->ticket_code = self::generateTicketCode();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artClass()
    {
        return $this->belongsTo(ArtClass::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    // Helper methods
    public static function generateTicketCode()
    {
        do {
            $code = 'FB-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('ticket_code', $code)->exists());

        return $code;
    }

    public function checkIn()
    {
        $this->update([
            'attendance_status' => 'attended',
            'checked_in_at' => now(),
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'attendance_status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function getIsConfirmedAttribute()
    {
        return $this->payment_status === 'completed';
    }

    public function getIsCheckedInAttribute()
    {
        return $this->attendance_status === 'attended';
    }

    public function getIsCancelledAttribute()
    {
        return $this->attendance_status === 'cancelled';
    }

    public function scopeConfirmed($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereHas('artClass', function ($q) {
            $q->where('class_date', '>', now());
        });
    }

    public function scopePast($query)
    {
        return $query->whereHas('artClass', function ($q) {
            $q->where('class_date', '<=', now());
        });
    }
}
