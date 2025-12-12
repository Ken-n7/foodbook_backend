<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        $userId = Auth::id();

        // Handle media_url: string or JSON array
        $rawMedia = $this->media_url;
        if (is_string($rawMedia) && str_starts_with($rawMedia, '[')) {
            $mediaUrls = json_decode($rawMedia, true) ?? [];
        } else {
            $mediaUrls = $rawMedia ? [$rawMedia] : [];
        }

        $media = array_map(function ($url) {
            if (!$url) return null;
            $isVideo = str_ends_with(strtolower($url), '.mp4') || str_ends_with(strtolower($url), '.mov');
            return [
                'url' => $url,
                'type' => $isVideo ? 'video' : 'image'
            ];
        }, array_filter($mediaUrls));

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'restaurant_id' => $this->restaurant_id,
            'caption' => $this->caption,
            'media_url' => count($mediaUrls) === 1 ? $mediaUrls[0] : $mediaUrls,
            'media' => array_values($media), // clean indexed array

            'rating' => $this->whenLoaded('ratings', fn() => $this->ratings->first()?->rating),

            'likes_count' => (int) $this->likes_count,
            'comments_count' => (int) $this->comments_count,
            'is_liked' => $userId ? $this->likes()->where('user_id', $userId)->exists() : false,

            'user' => new UserResource($this->whenLoaded('user')),
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),

            'created_at' => $this->created_at->toDateTimeString(),
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}