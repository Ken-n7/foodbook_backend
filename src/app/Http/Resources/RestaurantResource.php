<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,

            'average_rating' => round((float) $this->average_rating, 1),
            'ratings_count' => (int) $this->ratings_count,
            'posts_count' => $this->whenCounted('posts'),

            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}