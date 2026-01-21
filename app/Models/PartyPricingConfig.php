<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyPricingConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'pricing_type',
        'base_price_cents',
        'minimum_guests',
        'maximum_guests',
        'tier_pricing',
        'lila_venue_fee_cents',
        'lila_venue_per_person_cents',
        'lila_venue_max_capacity',
        'custom_painting_fee_cents',
        'description',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'base_price_cents' => 'integer',
        'minimum_guests' => 'integer',
        'maximum_guests' => 'integer',
        'tier_pricing' => 'array',
        'lila_venue_fee_cents' => 'integer',
        'lila_venue_per_person_cents' => 'integer',
        'lila_venue_max_capacity' => 'integer',
        'custom_painting_fee_cents' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Relationships
    public function partyBookings()
    {
        return $this->hasMany(PartyBooking::class);
    }

    // Pricing calculation methods

    /**
     * Calculate base price for a given guest count.
     */
    public function calculateBasePrice(int $guestCount): int
    {
        if ($this->pricing_type === 'custom_quote') {
            return 0; // Custom quotes are set manually
        }

        if ($this->pricing_type === 'flat_per_person') {
            return ($this->base_price_cents ?? 0) * $guestCount;
        }

        // Tiered pricing
        if ($this->pricing_type === 'tiered' && is_array($this->tier_pricing)) {
            foreach ($this->tier_pricing as $tier) {
                $min = $tier['min'] ?? 0;
                $max = $tier['max'] ?? PHP_INT_MAX;
                if ($guestCount >= $min && $guestCount <= $max) {
                    return ($tier['price_cents'] ?? 0) * $guestCount;
                }
            }
        }

        return 0;
    }

    /**
     * Calculate venue fee based on location type.
     */
    public function calculateVenueFee(string $locationType, int $guestCount): int
    {
        if ($locationType !== 'lila_hosts') {
            return 0;
        }

        // Check capacity limit
        if ($guestCount > $this->lila_venue_max_capacity) {
            return 0; // Over capacity, cannot host
        }

        // Flat venue fee takes precedence
        if ($this->lila_venue_fee_cents) {
            return $this->lila_venue_fee_cents;
        }

        // Per-person venue fee
        if ($this->lila_venue_per_person_cents) {
            return $this->lila_venue_per_person_cents * $guestCount;
        }

        return 0;
    }

    /**
     * Check if Lila can host this number of guests.
     */
    public function canLilaHost(int $guestCount): bool
    {
        return $guestCount <= $this->lila_venue_max_capacity;
    }

    /**
     * Get the per-person rate for display.
     */
    public function getPerPersonRateAttribute(): ?int
    {
        if ($this->pricing_type === 'flat_per_person') {
            return $this->base_price_cents;
        }
        return null;
    }

    // Accessors
    public function getFormattedBasePriceAttribute()
    {
        return '$' . number_format(($this->base_price_cents ?? 0) / 100, 2);
    }

    public function getFormattedVenueFeeAttribute()
    {
        return '$' . number_format(($this->lila_venue_fee_cents ?? 0) / 100, 2);
    }

    public function getFormattedCustomPaintingFeeAttribute()
    {
        return '$' . number_format(($this->custom_painting_fee_cents ?? 0) / 100, 2);
    }

    public function getPricingTypeDisplayAttribute()
    {
        return match ($this->pricing_type) {
            'flat_per_person' => 'Flat Per Person',
            'tiered' => 'Tiered Pricing',
            'custom_quote' => 'Custom Quote',
            default => ucfirst($this->pricing_type),
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
