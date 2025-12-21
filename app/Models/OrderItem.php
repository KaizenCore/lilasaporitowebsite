<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_title',
        'product_type',
        'quantity',
        'price_cents',
        'total_cents',
        'digital_download_url',
        'download_count',
    ];

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Generate a secure download URL for digital products
     */
    public function generateDownloadUrl()
    {
        $token = Str::random(64);
        $this->update(['digital_download_url' => $token]);
        return route('download', $token);
    }

    /**
     * Accessors
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price_cents / 100, 2);
    }

    public function getFormattedTotalAttribute()
    {
        return '$' . number_format($this->total_cents / 100, 2);
    }
}
