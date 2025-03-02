<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'vendor_id' => $this->pivot->vendor_id ?? null,
            'region_id' => $this->pivot->region_id ?? $this->id,
            'delivery_cost' => $this->pivot->delivery_cost ?? null,
            'discount' => $this->pivot->discount ?? null,
            'description' => $this->pivot->description ?? null,
        ];
    }
}
