<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'vendor_id',
        'product_name',
        'description',
    ];

    /**
     * Get all categories associated with this product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the vendor who owns this product.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }

    public function usersWhoFavorited()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0; 
    }
}
