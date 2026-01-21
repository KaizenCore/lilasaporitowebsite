<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartyBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number',
        'user_id',
        'status',
        'preferred_date',
        'preferred_time',
        'alternate_date',
        'alternate_time',
        'confirmed_date',
        'confirmed_time',
        'location_type',
        'customer_address',
        'customer_city',
        'customer_state',
        'customer_zip',
        'guest_count',
        'event_details',
        'event_type',
        'honoree_name',
        'honoree_age',
        'party_painting_id',
        'wants_custom_painting',
        'custom_painting_description',
        'party_pricing_config_id',
        'selected_addon_ids',
        'quoted_subtotal_cents',
        'quoted_addons_cents',
        'quoted_venue_fee_cents',
        'quoted_custom_painting_fee_cents',
        'quoted_adjustment_cents',
        'quoted_total_cents',
        'quote_notes',
        'quote_sent_at',
        'quote_expires_at',
        'deposit_required_cents',
        'deposit_paid_cents',
        'deposit_paid_at',
        'total_paid_cents',
        'payment_status',
        'contact_name',
        'contact_email',
        'contact_phone',
        'admin_notes',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_time' => 'datetime:H:i',
        'alternate_date' => 'date',
        'alternate_time' => 'datetime:H:i',
        'confirmed_date' => 'date',
        'confirmed_time' => 'datetime:H:i',
        'guest_count' => 'integer',
        'honoree_age' => 'integer',
        'wants_custom_painting' => 'boolean',
        'selected_addon_ids' => 'array',
        'quoted_subtotal_cents' => 'integer',
        'quoted_addons_cents' => 'integer',
        'quoted_venue_fee_cents' => 'integer',
        'quoted_custom_painting_fee_cents' => 'integer',
        'quoted_adjustment_cents' => 'integer',
        'quoted_total_cents' => 'integer',
        'quote_sent_at' => 'datetime',
        'quote_expires_at' => 'datetime',
        'deposit_required_cents' => 'integer',
        'deposit_paid_cents' => 'integer',
        'deposit_paid_at' => 'datetime',
        'total_paid_cents' => 'integer',
        'cancelled_at' => 'datetime',
    ];

    const STATUS_INQUIRY = 'inquiry';
    const STATUS_QUOTED = 'quoted';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DEPOSIT_PAID = 'deposit_paid';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DECLINED = 'declined';

    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_DEPOSIT_PAID = 'deposit_paid';
    const PAYMENT_PAID = 'paid';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = self::generateBookingNumber();
            }
        });
    }

    /**
     * Generate unique booking number (FBP-XXXX).
     */
    public static function generateBookingNumber(): string
    {
        do {
            $number = 'FBP-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('booking_number', $number)->exists());

        return $number;
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function partyPainting()
    {
        return $this->belongsTo(PartyPainting::class);
    }

    public function pricingConfig()
    {
        return $this->belongsTo(PartyPricingConfig::class, 'party_pricing_config_id');
    }

    public function payments()
    {
        return $this->hasMany(PartyPayment::class);
    }

    public function availabilitySlot()
    {
        return $this->hasOne(PartyAvailabilitySlot::class);
    }

    /**
     * Get the selected addons.
     */
    public function getSelectedAddons()
    {
        if (empty($this->selected_addon_ids)) {
            return collect();
        }
        return PartyAddon::whereIn('id', $this->selected_addon_ids)->get();
    }

    // Status helpers
    public function canSendQuote(): bool
    {
        return in_array($this->status, [self::STATUS_INQUIRY]);
    }

    public function canAcceptQuote(): bool
    {
        return $this->status === self::STATUS_QUOTED
            && $this->quoted_total_cents > 0
            && (!$this->quote_expires_at || $this->quote_expires_at->isFuture());
    }

    public function canPayDeposit(): bool
    {
        return in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_QUOTED])
            && $this->deposit_required_cents > 0
            && $this->deposit_paid_cents < $this->deposit_required_cents;
    }

    public function canPayFinal(): bool
    {
        return in_array($this->status, [self::STATUS_DEPOSIT_PAID, self::STATUS_ACCEPTED])
            && $this->total_paid_cents < $this->quoted_total_cents;
    }

    public function canCancel(): bool
    {
        return !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_DECLINED, self::STATUS_COMPLETED]);
    }

    public function isQuoteExpired(): bool
    {
        return $this->quote_expires_at && $this->quote_expires_at->isPast();
    }

    public function getRemainingBalanceAttribute(): int
    {
        return max(0, ($this->quoted_total_cents ?? 0) - $this->total_paid_cents);
    }

    // Accessors
    public function getFormattedQuotedTotalAttribute()
    {
        return '$' . number_format(($this->quoted_total_cents ?? 0) / 100, 2);
    }

    public function getFormattedDepositRequiredAttribute()
    {
        return '$' . number_format(($this->deposit_required_cents ?? 0) / 100, 2);
    }

    public function getFormattedTotalPaidAttribute()
    {
        return '$' . number_format($this->total_paid_cents / 100, 2);
    }

    public function getFormattedRemainingBalanceAttribute()
    {
        return '$' . number_format($this->remaining_balance / 100, 2);
    }

    public function getStatusBadgeColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_INQUIRY => 'blue',
            self::STATUS_QUOTED => 'yellow',
            self::STATUS_ACCEPTED => 'purple',
            self::STATUS_DEPOSIT_PAID => 'indigo',
            self::STATUS_CONFIRMED => 'green',
            self::STATUS_COMPLETED => 'gray',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_DECLINED => 'red',
            default => 'gray',
        };
    }

    public function getStatusDisplayAttribute()
    {
        return match ($this->status) {
            self::STATUS_INQUIRY => 'New Inquiry',
            self::STATUS_QUOTED => 'Quote Sent',
            self::STATUS_ACCEPTED => 'Quote Accepted',
            self::STATUS_DEPOSIT_PAID => 'Deposit Paid',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_DECLINED => 'Declined',
            default => ucfirst($this->status),
        };
    }

    public function getEventTypeDisplayAttribute()
    {
        return match ($this->event_type) {
            'birthday' => 'Birthday Party',
            'corporate' => 'Corporate Event',
            'bridal_shower' => 'Bridal Shower',
            'bachelorette' => 'Bachelorette Party',
            'team_building' => 'Team Building',
            'other' => 'Other Event',
            default => ucfirst($this->event_type ?? 'Event'),
        };
    }

    public function getLocationTypeDisplayAttribute()
    {
        return $this->location_type === 'lila_hosts' ? "Lila's Studio" : 'Your Location';
    }

    public function getFullCustomerAddressAttribute()
    {
        if ($this->location_type === 'lila_hosts') {
            return null;
        }

        $parts = array_filter([
            $this->customer_address,
            $this->customer_city,
            $this->customer_state,
            $this->customer_zip,
        ]);

        return implode(', ', $parts);
    }

    // Scopes
    public function scopeInquiries($query)
    {
        return $query->where('status', self::STATUS_INQUIRY);
    }

    public function scopePendingQuotes($query)
    {
        return $query->where('status', self::STATUS_QUOTED);
    }

    public function scopeConfirmed($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_DEPOSIT_PAID]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where(function ($q) {
            $q->where('confirmed_date', '>=', now()->toDateString())
                ->orWhere(function ($q2) {
                    $q2->whereNull('confirmed_date')
                        ->where('preferred_date', '>=', now()->toDateString());
                });
        });
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_DECLINED, self::STATUS_COMPLETED]);
    }
}
