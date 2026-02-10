<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'art_class_id',
        'rating',
        'title',
        'body',
        'is_approved',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artClass()
    {
        return $this->belongsTo(ArtClass::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeForClass($query, $id)
    {
        return $query->where('art_class_id', $id);
    }

    public function approve()
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }
}
