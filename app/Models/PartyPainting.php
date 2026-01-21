<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PartyPainting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_path',
        'difficulty_level',
        'estimated_duration_minutes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'estimated_duration_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($painting) {
            if (empty($painting->slug)) {
                $painting->slug = Str::slug($painting->title);
            }
        });
    }

    // Relationships
    public function partyBookings()
    {
        return $this->hasMany(PartyBooking::class);
    }

    // Accessors
    public function getDifficultyBadgeColorAttribute()
    {
        return match ($this->difficulty_level) {
            'beginner' => 'green',
            'intermediate' => 'yellow',
            'advanced' => 'red',
            default => 'gray',
        };
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->estimated_duration_minutes / 60);
        $minutes = $this->estimated_duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        }
        return "{$minutes}m";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('title', 'asc');
    }

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }
}
