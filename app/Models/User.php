<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be guarded from mass assignment.
     * is_admin must be set explicitly to prevent privilege escalation.
     *
     * @var list<string>
     */
    protected $guarded = [
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function createdClasses()
    {
        return $this->hasMany(ArtClass::class, 'created_by');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->is_admin === true;
    }

    public function upcomingBookings()
    {
        return $this->bookings()
            ->confirmed()
            ->upcoming()
            ->with('artClass')
            ->orderBy('created_at', 'desc');
    }

    public function pastBookings()
    {
        return $this->bookings()
            ->confirmed()
            ->past()
            ->with('artClass')
            ->orderBy('created_at', 'desc');
    }
}
