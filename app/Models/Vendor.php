<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Enums\VendorStatus;

class Vendor extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = ['brand_name', 'image', 'description', 'phone', 'status'];
    protected $casts = [
        'status' => VendorStatus::class,
    ];

    protected $dates = ['deleted_at'];

    public function regions()
    {
        return $this->belongsToMany(Region::class, 'region_vendor')
            ->withPivot('delivery_cost', 'discount', 'description');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_vendor');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'vendor_id', 'user_id');
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'vendor_promotion')
            ->withPivot('start_date', 'end_date', 'status')
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('vendor_images')->singleFile();
    }

    public function getImageUrl()
    {
        $url = $this->getFirstMediaUrl('vendor_images');

        return !empty($url) ? $url : asset('images/vendor-placeholder.jpg');
    }
}
