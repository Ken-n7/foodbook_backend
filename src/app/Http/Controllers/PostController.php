<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function userPosts(User $user)
    {
        $posts = Post::where('user_id', $user->id)
            ->with([
                'user',
                'restaurant',
                'comments.user',
                'likes'
            ])
            ->withCount(['likes', 'comments'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if authenticated user has liked each post
        if (Auth::check()) {
            $posts->each(function ($post) {
                $post->is_liked = $post->likes()->where('user_id', Auth::id())->exists();
            });
        }

        return PostResource::collection($posts);
    }

    public function index()
    {
        $posts = Post::with(['user', 'restaurant'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(15);

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        $post->load(['user', 'restaurant'])
            ->loadCount(['likes', 'comments']);

        return new PostResource($post);
    }

    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $ratingData = $request->validate(['rating' => 'integer|min:1|max:5']);

        $mediaUrls = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts_media', 'public');
                $mediaUrls[] = asset('storage/' . $path);
            }
        }

        $data['media_url'] = count($mediaUrls) === 1 ? $mediaUrls[0] : json_encode($mediaUrls);

        $post = Post::create([
            'user_id' => $data['user_id'],
            'restaurant_id' => $data['restaurant_id'],
            'caption' => $data['caption'] ?? null,
            'media_url' => $data['media_url'],
        ]);

        $rating = $post->restaurant->ratings()->create([
            'user_id' => $data['user_id'],
            'post_id' => $post->id,
            'rating' => $ratingData['rating'],
        ]);

        $this->updateRestaurantRating($post->restaurant);

        return response()->json([
            'post' => [
                'id' => $post->id,
                'media_type' => $this->getMediaType($mediaUrls),
                'caption' => $post->content,
                'restaurant_id' => $post->restaurant_id,
                'media_url' => $post->media_url,
            ],
            'rating' => [
                'id' => $rating->id,
                'rating' => $rating->rating,
                'restaurant_id' => $rating->restaurant_id,
                'post_id' => $rating->post_id,
            ],
        ], 201);
    }


    // public function show(Post $post)
    // {
    //     $post->load(['user', 'restaurant', 'comments', 'likes', 'ratings']);
    //     return response()->json($post);
    // }

    public function update(PostRequest $request, Post $post)
    {
        // $this->authorize('update', $post); // You can create a policy for this

        $post->update($request->validated());
        return response()->json($post);
    }

    public function destroy(Post $post)
    {
        // $this->authorize('delete', $post);
        $post->delete();
        return response()->json(null, 204);
    }


    protected function getMediaType(array $mediaUrls): string
    {
        if (empty($mediaUrls)) {
            return 'none';
        }

        // Simple check based on file extension
        $firstUrl = $mediaUrls[0];
        $extension = pathinfo($firstUrl, PATHINFO_EXTENSION);

        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
            return 'image';
        } elseif (in_array(strtolower($extension), ['mp4', 'mov', 'avi'])) {
            return 'video';
        }

        return 'unknown';
    }

    protected function updateRestaurantRating($restaurant)
    {
        $ratings = $restaurant->ratings()->pluck('rating');
        $restaurant->ratings_count = $ratings->count();
        $restaurant->average_rating = $ratings->avg() ?? 0;
        $restaurant->save();
    }

    // PostController.php
    public function toggleLike(Post $post)
    {
        $user = Auth::user();

        $like = $post->likes()->where('user_id', $user->id)->first();


        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
            $isLiked = false;
        } else {
            $post->likes()->create(['post_id' => $post->id, 'user_id' => $user->id]);
            $post->increment('likes_count');
            $isLiked = true;
        }

        return response()->json([
            'is_liked' => $isLiked,
            'likes_count' => $post->likes_count
        ]);
    }
}
