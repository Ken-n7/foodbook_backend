<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeRequest;
use App\Models\Like;
use  Illuminate\Support\Facades\Auth;   

class LikeController extends Controller
{
    public function store(LikeRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $like = Like::firstOrCreate($data);
        return response()->json($like, 201);
    }

    public function destroy(Like $like)
    {
        // $this->authorize('delete', $like);
        $like->delete();
        return response()->json(null, 204);
    }
}
