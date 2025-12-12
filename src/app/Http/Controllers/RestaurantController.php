<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Resources\RestaurantResource;

use Illuminate\Support\Facades\Log;


class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::withCount('posts')
            ->orderByDesc('average_rating')
            ->orderByDesc('posts_count')
            ->paginate(20);

        return RestaurantResource::collection($restaurants);
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->loadCount('posts');
        return new RestaurantResource($restaurant);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $restaurant = Restaurant::create($data);

        return new RestaurantResource($restaurant);
    }

    public function search(Request $request)
    {
        $query = $request->query('q', '');
        Log::alert('line 47');
        if (!$query || strlen($query) < 2) {
            Log::alert('line 49');
            return response()->json([]);
        }

        try {
            $restaurants = Restaurant::query()
                ->select(['id', 'name', 'address', 'latitude', 'longitude', 'average_rating', 'created_at'])
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage(), [
                'exception' => $e,
                'query' => $query
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred.'
            ], 500);
        }

        return RestaurantResource::collection($restaurants);
    }


    // PUT/PATCH /restaurants/{restaurant}
    public function update(Request $request, Restaurant $restaurant)
    {
        $data = $request->validate([
            'name' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
        ]);

        $restaurant->update($data);

        return $restaurant;
    }

    // DELETE /restaurants/{restaurant}
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted']);
    }
}
