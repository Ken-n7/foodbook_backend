<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Restaurant;

class RestaurantSeeder extends Seeder
{
    public function run()
    {
        // Path to your JSON file
        $jsonPath = database_path('data/tacloban_restaurants.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("JSON file not found at $jsonPath");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (!isset($data['restaurants']) || !is_array($data['restaurants'])) {
            $this->command->error("Invalid JSON format: 'restaurants' key missing or not an array");
            return;
        }

        foreach ($data['restaurants'] as $restaurant) {
            Restaurant::updateOrCreate(
                // ['id' => $restaurant['id']],  // Use your JSON id as primary key if possible, else skip
                [
                    'name' => $restaurant['name'],
                    'latitude' => $restaurant['latitude'],
                    'longitude' => $restaurant['longitude'],
                    'average_rating' => 0,      // default values
                    'ratings_count' => 0,
                ]
            );
        }

        $this->command->info("Restaurants seeded successfully.");
    }
}
