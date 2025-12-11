<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    // GET /restaurants
    public function index()
    {
        return Restaurant::all();
    }

    // POST /restaurants
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $restaurant = Restaurant::create($data);

        return response()->json($restaurant, 201);
    }

    // GET /restaurants/{restaurant}
    public function show(Restaurant $restaurant)
    {
        return $restaurant;
    }

        public function search(Request $request)
    {
        $query = $request->query('q', '');

        if (empty($query)) {
            return response()->json([]);
        }

        $restaurants = Restaurant::where('name', 'LIKE', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name', 'latitude', 'longitude']); // only necessary fields

        return response()->json($restaurants);
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
