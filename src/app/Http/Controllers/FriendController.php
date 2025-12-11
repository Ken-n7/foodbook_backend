<?php

namespace App\Http\Controllers;

use App\Http\Requests\FriendRequest;
use App\Models\Friend;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function store(FriendRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $friend = Friend::firstOrCreate([
            'user_id' => $data['user_id'],
            'friend_id' => $data['friend_id'],
        ], [
            'status' => $data['status'] ?? 'pending',
        ]);

        return response()->json($friend, 201);
    }

    public function update(FriendRequest $request, Friend $friend)
    {
        // Only the friend recipient can accept
        // $this->authorize('update', $friend);

        $friend->update($request->validated());
        return response()->json($friend);
    }

    public function destroy(Friend $friend)
    {
        // $this->authorize('delete', $friend);
        $friend->delete();
        return response()->json(null, 204);
    }
}
