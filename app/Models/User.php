<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Laravel\Sanctum\HasApiTokens;

use Spatie\MediaLibrary\InteractsWithMedia;
use App\Enums\Gender;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, HasApiTokens, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'gender',
        'is_active',
        'role',
        'verification_token',
        'is_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token',
        'token_expiration',
    ];

    /**
     * Default attributes for new models.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'gender' => Gender::Other->value, // Default gender
        'is_active' => false,
        'role' => 'user',
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
            'gender' => Gender::class, // Cast the gender attribute to the Gender enum
            'is_active' => 'boolean',
        ];
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'user_vendor');
    }

    public function followedVendors()
    {
        return $this->belongsToMany(Vendor::class, 'follows', 'user_id', 'vendor_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites', 'user_id', 'product_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    /**
     * Register media collections for the user.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
            ->singleFile();
    }

    /**
     * Get the URL of the user's profile picture.
     * Returns a gender-specific placeholder if no profile picture is set.
     */
    public function getProfilePictureUrl(): string
    {
        // If the user has a profile picture, return its URL
        if ($this->hasMedia('profile_pictures')) {
            return $this->getFirstMediaUrl('profile_pictures');
        }

        // Return a gender-specific placeholder based on the user's gender
        return match ($this->gender) {
            Gender::Male => asset('images/male-placeholder.png'),
            Gender::Female => asset('images/female-placeholder.png'),
            default => asset('images/other-placeholder.png'),
        };
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
