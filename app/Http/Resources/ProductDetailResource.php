<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'size' => $this->size,
            'color' => $this->color,
            'price' => $this->price,
            'discount' => $this->discount,
            'stock' => $this->stock,
            'material' => $this->material,
            'images' => $this->getMedia('product_images')->map(function ($media) {
                return $media->getUrl();
            }),
        ];
    }
}

