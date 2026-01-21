<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyAvailabilitySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'status',
        'block_reason',
        'party_booking_id',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_BOOKED = 'booked';
    const STATUS_BLOCKED = 'blocked';

    // Relationships
    public function partyBooking()
    {
        return $this->belongsTo(PartyBooking::class);
    }

    // Helpers
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isBooked(): bool
    {
        return $this->status === self::STATUS_BOOKED;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    public function markAsBooked(PartyBooking $booking): void
    {
        $this->update([
            'status' => self::STATUS_BOOKED,
            'party_booking_id' => $booking->id,
        ]);
    }

    public function markAsAvailable(): void
    {
        $this->update([
            'status' => self::STATUS_AVAILABLE,
            'party_booking_id' => null,
            'block_reason' => null,
        ]);
    }

    public function markAsBlocked(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_BLOCKED,
            'block_reason' => $reason,
            'party_booking_id' => null,
        ]);
    }

    // Accessors
    public function getFormattedTimeRangeAttribute()
    {
        $start = $this->start_time->format('g:i A');
        $end = $this->end_time->format('g:i A');
        return "{$start} - {$end}";
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('l, F j, Y');
    }

    public function getStatusBadgeColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'green',
            self::STATUS_BOOKED => 'blue',
            self::STATUS_BLOCKED => 'red',
            default => 'gray',
        };
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeBooked($query)
    {
        return $query->where('status', self::STATUS_BOOKED);
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', self::STATUS_BLOCKED);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('date', 'asc')->orderBy('start_time', 'asc');
    }
}
