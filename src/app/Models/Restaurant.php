<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'average_rating',
        'ratings_count',
    ];

    protected $withCount = ['posts']; 

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
