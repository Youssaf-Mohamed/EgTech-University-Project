<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
