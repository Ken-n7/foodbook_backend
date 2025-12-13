<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        // Load relationships with counts
        $user->loadCount(['posts', 'friends', 'friendOf']);

        return new UserResource($user);
    }

    public function update(UserRequest $request, User $user)
    {
        // Check if the authenticated user is updating their own profile
        if ($request->user()->id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $data = $request->validated();

        // Handle password update
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);
        $user->loadCount(['posts', 'friends', 'friendOf']);

        return new UserResource($user);
    }

    public function destroy(User $user, Request $request)
    {
        // Check if the authenticated user is deleting their own account
        if ($request->user()->id !== $user->id && !$request->user()->is_admin) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json(null, 204);
    }

    /**
     * Get user's followers
     */
    public function followers(User $user)
    {
        $followers = $user->friendOf()->with(['posts'])->get();
        return UserResource::collection($followers);
    }

    /**
     * Get users that this user is following
     */
    public function following(User $user)
    {
        $following = $user->friends()->with(['posts'])->get();
        return UserResource::collection($following);
    }

    /**
     * Search users by name
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $users = User::where('name', 'LIKE', "%{$query}%")
            ->withCount(['posts', 'friends', 'friendOf'])
            ->limit(20)
            ->get();

        return UserResource::collection($users);
    }
}
