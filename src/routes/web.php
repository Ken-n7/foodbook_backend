<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

Route::middleware(['api', 'auth:api'])->get('/me', function () {
    return auth()->user();
});

Route::apiResource('recipes', RecipeController::class)->only(['index', 'show']);

Route::middleware(['api', 'auth:api'])->group(function () {
    Route::apiResource('recipes', RecipeController::class)->except(['index', 'show']);
    Route::apiResource('comments', CommentController::class)->except(['index', 'show']);
    Route::apiResource('ratings', RatingController::class)->only(['store', 'update', 'destroy']);
});









// // Public routes (no auth required)
// Route::get('/recipes', [RecipeController::class, 'index']); // Browse all recipes
// Route::get('/recipes/{recipe}', [RecipeController::class, 'show']); // View single recipe

// // Protected routes (auth required)
// Route::middleware(['api', 'auth:api'])->group(function () {
//     // Recipe management
//     Route::post('/recipes', [RecipeController::class, 'store']);
//     Route::put('/recipes/{recipe}', [RecipeController::class, 'update']); 
//     Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy']); 
    
//     // Comments
//     Route::post('/recipes/{recipe}/comments', [CommentController::class, 'store']);
//     Route::put('/comments/{comment}', [CommentController::class, 'update']);
//     Route::delete('/comments/{comment}', [CommentController::class, 'destroy']); 

//     // Ratings
//     Route::post('/recipes/{recipe}/ratings', [RatingController::class, 'store']);
//     Route::put('/ratings/{rating}', [RatingController::class, 'update']); 
//     Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);
// });