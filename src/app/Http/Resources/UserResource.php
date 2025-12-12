<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($request->user()?->id === $this->id, $this->email),
            'profile_picture' => $this->profile_picture
                ? asset('storage/' . $this->profile_picture)
                : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff',
            'bio' => $this->bio ?? '',
            'is_admin' => (bool) $this->is_admin,

            'posts_count' => $this->whenCounted('posts'),
            'followers_count' => $this->whenCounted('friendOf'),
            'following_count' => $this->whenCounted('friends'),

            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}