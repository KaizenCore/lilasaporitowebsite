<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'short_description',
        'price_cents',
        'compare_at_price_cents',
        'product_type',
        'image_path',
        'gallery_images',
        'digital_file_path',
        'stock_quantity',
        'sku',
        'weight_grams',
        'requires_shipping',
        'is_featured',
        'status',
        'created_by',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'requires_shipping' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Boot the model and auto-generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('title') && empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('stock_quantity')
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    public function scopePhysical($query)
    {
        return $query->where('product_type', 'physical');
    }

    public function scopeDigital($query)
    {
        return $query->where('product_type', 'digital');
    }

    /**
     * Accessors
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price_cents / 100, 2);
    }

    public function getIsOnSaleAttribute()
    {
        return !is_null($this->compare_at_price_cents) && $this->compare_at_price_cents > $this->price_cents;
    }

    public function getSalePercentageAttribute()
    {
        if (!$this->is_on_sale) {
            return 0;
        }

        $discount = $this->compare_at_price_cents - $this->price_cents;
        return round(($discount / $this->compare_at_price_cents) * 100);
    }

    public function getIsOutOfStockAttribute()
    {
        // Null stock_quantity means unlimited stock
        if (is_null($this->stock_quantity)) {
            return false;
        }

        return $this->stock_quantity <= 0;
    }

    /**
     * Stock Management Methods
     */
    public function decrementStock($quantity)
    {
        if (is_null($this->stock_quantity)) {
            return; // Unlimited stock, nothing to decrement
        }

        $this->decrement('stock_quantity', $quantity);
    }

    public function incrementStock($quantity)
    {
        if (is_null($this->stock_quantity)) {
            return; // Unlimited stock, nothing to increment
        }

        $this->increment('stock_quantity', $quantity);
    }
}
