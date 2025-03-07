<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Advertisement extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'brief',
        'content',
        'redirect_to',
        'target_screen',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('advertisement_image')
            ->singleFile();
    }

    public function getImageUrl()
    {
        return $this->getFirstMediaUrl('advertisement_image');
    }
}
