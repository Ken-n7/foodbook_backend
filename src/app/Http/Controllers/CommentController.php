<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $comment = Comment::create($data);
        return response()->json($comment, 201);
    }

    public function destroy(Comment $comment)
    {
        // $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json(null, 204);
    }
}
