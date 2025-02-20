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
    use HasFactory, Notifiable,HasApiTokens, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'gender',
        'email',
        'password',
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
     * Default attributes for new models.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'gender' => Gender::Other->value, // Default gender
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
        ];
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
}