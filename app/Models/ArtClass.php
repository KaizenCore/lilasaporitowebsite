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
        'gallery_images',
        'class_date',
        'duration_minutes',
        'price_cents',
        'capacity',
        'location',
        'location_public',
        'status',
        'is_party_event',
        'small_party_price_cents',
        'small_party_size',
        'large_party_price_cents',
        'large_party_size',
        'additional_guest_price_cents',
        'max_party_size',
        'party_addons',
        'created_by',
    ];

    protected $casts = [
        'class_date' => 'datetime',
        'price_cents' => 'integer',
        'capacity' => 'integer',
        'duration_minutes' => 'integer',
        'is_party_event' => 'boolean',
        'small_party_price_cents' => 'integer',
        'small_party_size' => 'integer',
        'large_party_price_cents' => 'integer',
        'large_party_size' => 'integer',
        'additional_guest_price_cents' => 'integer',
        'max_party_size' => 'integer',
        'party_addons' => 'array',
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

    /**
     * Get the public display location (general area).
     * Falls back to full location if public location not set.
     */
    public function getDisplayLocationAttribute()
    {
        return $this->location_public ?: $this->location;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        // Show classes until end of the day they're scheduled
        // This ensures classes remain visible throughout their scheduled day
        return $query->whereDate('class_date', '>=', now()->toDateString());
    }

    public function scopeAvailable($query)
    {
        return $query->published()
            ->upcoming()
            ->orderBy('class_date', 'asc');
    }

    // Party pricing methods

    /**
     * Get formatted small party price.
     */
    public function getFormattedSmallPartyPriceAttribute()
    {
        return '$' . number_format(($this->small_party_price_cents ?? 0) / 100, 2);
    }

    /**
     * Get formatted large party price.
     */
    public function getFormattedLargePartyPriceAttribute()
    {
        return '$' . number_format(($this->large_party_price_cents ?? 0) / 100, 2);
    }

    /**
     * Get formatted additional guest price.
     */
    public function getFormattedAdditionalGuestPriceAttribute()
    {
        return '$' . number_format(($this->additional_guest_price_cents ?? 0) / 100, 2);
    }

    /**
     * Calculate party price based on package and guest count.
     *
     * @param string $package 'small' or 'large'
     * @param int $guestCount Total number of guests
     * @return int Price in cents
     */
    public function calculatePartyPrice(string $package, int $guestCount): int
    {
        if ($package === 'small') {
            $basePrice = $this->small_party_price_cents ?? 0;
            $includedGuests = $this->small_party_size ?? 0;
        } else {
            $basePrice = $this->large_party_price_cents ?? 0;
            $includedGuests = $this->large_party_size ?? 0;
        }

        $extraGuests = max(0, $guestCount - $includedGuests);
        $extraCost = $extraGuests * ($this->additional_guest_price_cents ?? 0);

        return $basePrice + $extraCost;
    }

    /**
     * Get formatted calculated party price.
     */
    public function getFormattedPartyPrice(string $package, int $guestCount): string
    {
        return '$' . number_format($this->calculatePartyPrice($package, $guestCount) / 100, 2);
    }

    /**
     * Calculate add-ons total based on selected add-ons and guest count.
     *
     * @param array $selectedAddonIndexes Array of addon indexes that are selected
     * @param int $guestCount Total number of guests
     * @return int Total add-ons price in cents
     */
    public function calculateAddonsTotal(array $selectedAddonIndexes, int $guestCount): int
    {
        $addons = $this->party_addons ?? [];
        $total = 0;

        foreach ($selectedAddonIndexes as $index) {
            if (isset($addons[$index])) {
                $total += ($addons[$index]['price_cents'] ?? 0) * $guestCount;
            }
        }

        return $total;
    }

    /**
     * Calculate full party price including add-ons.
     *
     * @param string $package 'small' or 'large'
     * @param int $guestCount Total number of guests
     * @param array $selectedAddonIndexes Array of addon indexes that are selected
     * @return int Total price in cents
     */
    public function calculateFullPartyPrice(string $package, int $guestCount, array $selectedAddonIndexes = []): int
    {
        $basePrice = $this->calculatePartyPrice($package, $guestCount);
        $addonsPrice = $this->calculateAddonsTotal($selectedAddonIndexes, $guestCount);

        return $basePrice + $addonsPrice;
    }
}
