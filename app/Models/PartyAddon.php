<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price_cents',
        'is_per_person',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'is_per_person' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Calculate addon price for given guest count.
     */
    public function calculatePrice(int $guestCount): int
    {
        if ($this->is_per_person) {
            return $this->price_cents * $guestCount;
        }
        return $this->price_cents;
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        $price = '$' . number_format($this->price_cents / 100, 2);
        if ($this->is_per_person) {
            return $price . '/person';
        }
        return $price;
    }

    public function getFormattedBasePriceAttribute()
    {
        return '$' . number_format($this->price_cents / 100, 2);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }
}
