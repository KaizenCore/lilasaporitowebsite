<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ArtClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'materials_included',
        'image_path',
        'class_date',
        'duration_minutes',
        'price_cents',
        'capacity',
        'location',
        'status',
        'created_by',
    ];

    protected $casts = [
        'class_date' => 'datetime',
        'price_cents' => 'integer',
        'capacity' => 'integer',
        'duration_minutes' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artClass) {
            if (empty($artClass->slug)) {
                $artClass->slug = Str::slug($artClass->title);
            }
        });
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Helper methods
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price_cents / 100, 2);
    }

    public function getSpotsAvailableAttribute()
    {
        $bookedCount = $this->bookings()
            ->where('payment_status', 'completed')
            ->whereIn('attendance_status', ['booked', 'attended'])
            ->count();

        return $this->capacity - $bookedCount;
    }

    public function getIsFullAttribute()
    {
        return $this->spots_available <= 0;
    }

    public function getIsUpcomingAttribute()
    {
        return $this->class_date->isFuture();
    }

    public function getIsPastAttribute()
    {
        return $this->class_date->isPast();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('class_date', '>', now());
    }

    public function scopeAvailable($query)
    {
        return $query->published()
            ->upcoming()
            ->orderBy('class_date', 'asc');
    }
}
