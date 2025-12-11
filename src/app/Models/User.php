<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'bio',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Posts created by user
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Ratings made by user
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Comments made by user
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Likes made by user
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Friends where user is the requester
    public function friends()
    {
        return $this->hasMany(Friend::class);
    }

    // Friendships where user is the friend (inverse)
    public function friendOf()
    {
        return $this->hasMany(Friend::class, 'friend_id');
    }

    // Reports made by user
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
