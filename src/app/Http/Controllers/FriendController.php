<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    /**
     * Follow a user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'friend_id' => 'required|exists:users,id'
        ]);

        $userId = Auth::id();
        $friendId = $validated['friend_id'];

        // Check if already following
        $existing = Friend::where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Already following this user',
                'friendship_id' => $existing->id
            ], 400);
        }

        // Can't follow yourself
        if ($userId === $friendId) {
            return response()->json([
                'message' => 'Cannot follow yourself'
            ], 400);
        }

        $friendship = Friend::create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => 'accepted' // Auto-accept for now
        ]);

        return response()->json([
            'message' => 'Successfully followed user',
            'friendship_id' => $friendship->id
        ], 201);
    }

    /**
     * Unfollow a user
     */
    public function destroy(Friend $friend)
    {
        // Check authorization
        if (Auth::id() !== $friend->user_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $friend->delete();

        return response()->json([
            'message' => 'Successfully unfollowed user'
        ], 200);
    }

    /**
     * Check if current user is following another user
     */
    public function checkFollowing(User $user)
    {
        $friendship = Friend::where('user_id', Auth::id())
            ->where('friend_id', $user->id)
            ->first();

        return response()->json([
            'is_following' => $friendship !== null,
            'friendship_id' => $friendship?->id
        ]);
    }

    /**
     * Update friendship status (for future friend request system)
     */
    public function update(Request $request, Friend $friend)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected'
        ]);

        // Only the friend (receiver) can update the status
        if (Auth::id() !== $friend->friend_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $friend->update($validated);

        return response()->json([
            'message' => 'Friendship status updated',
            'friendship' => $friend
        ]);
    }
}