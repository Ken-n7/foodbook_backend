<?php

namespace App\Http\Controllers;

use App\Http\Requests\RatingRequest;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(RatingRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        // Check if rating exists for this user and target (post or restaurant)
        $rating = Rating::where('user_id', Auth::id())
            ->when(isset($data['post_id']), fn($q) => $q->where('post_id', $data['post_id']))
            ->when(isset($data['restaurant_id']), fn($q) => $q->where('restaurant_id', $data['restaurant_id']))
            ->first();

        if ($rating) {
            $rating->update(['rating' => $data['rating']]);
            return response()->json($rating);
        }

        $rating = Rating::create($data);
        return response()->json($rating, 201);
    }

    public function destroy(Rating $rating)
    {
        // $this->authorize('delete', $rating);
        $rating->delete();
        return response()->json(null, 204);
    }
}
