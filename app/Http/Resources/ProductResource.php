<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->product_name,
            'description' => $this->description,
            'average_rating' => $this->average_rating,
            'details' => ProductDetailResource::collection($this->whenLoaded('details')),
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
            'region' => new RegionResource($this->whenLoaded('region')),
        ];
    }
}


