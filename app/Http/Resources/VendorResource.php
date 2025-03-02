<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'brand_name' => $this->brand_name,
            'vendor_image' => $this->getImageUrl(),
            'description' => $this->description,
            'phone' => $this->phone,
            'status' => $this->status,
            'regions' => RegionResource::collection($this->regions),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
