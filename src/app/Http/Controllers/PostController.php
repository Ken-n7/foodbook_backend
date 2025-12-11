<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'restaurant'])->latest()->paginate(10);
        return response()->json($posts);
    }

    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $post = Post::create($data);
        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        $post->load(['user', 'restaurant', 'comments', 'likes', 'ratings']);
        return response()->json($post);
    }

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
}
