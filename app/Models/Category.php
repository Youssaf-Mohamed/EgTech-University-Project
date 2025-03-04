<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'category_name',
        'description',
    ];

    /**
     * Get all products associated with this category.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    /**
     * Register media collections for the category.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_images')->singleFile();
    }

    /**
     * Get the URL of the category image.
     */
    public function getImageUrl()
    {
        $url = $this->getFirstMediaUrl('category_images');

        return !empty($url) ? $url : asset('images/category-placeholder.png');
    }
}
