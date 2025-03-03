<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductDetail extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'price',
        'discount',
        'stock',
        'material',
    ];

    /**
     * Get the product that owns this detail.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Register media collections for product details.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images');
    }

    /**
     * Get the URL of the first image for this product detail.
     */
    public function getImageUrl()
    {
        $url = $this->getFirstMediaUrl('product_images');

        return !empty($url) ? $url : asset('images/product-placeholder.jpg');
    }
}
