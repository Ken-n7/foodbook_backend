<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestaurantController;

// Public Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/restaurants/search', [RestaurantController::class, 'search']);

// Public user profile view
Route::get('/users/{user}', [UserController::class, 'show']);

// Public routes for social features
Route::get('/users/{user}/followers', [UserController::class, 'followers']);
Route::get('/users/{user}/following', [UserController::class, 'following']);

Route::post('/posts/{post}/toggle-like', [PostController::class, 'toggleLike'])
    ->middleware('auth:sanctum');

// Routes protected by Sanctum auth
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/user', [AuthController::class, 'user']);

    // User resource routes (update, delete - show is public)
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    
    // User search
    Route::get('/users/search', [UserController::class, 'search']);

    // Restaurant routes
    Route::apiResource('restaurants', RestaurantController::class);
    
    // Post routes
    Route::apiResource('posts', PostController::class)->except('toggleLike');
    Route::get('/users/{user}/posts', [PostController::class, 'userPosts']);

    // Rating routes
    Route::apiResource('ratings', RatingController::class)->only([
        'store',
        'destroy'
    ]);

    // Comment routes
    Route::apiResource('comments', CommentController::class)->only([
        'store',
        'destroy'
    ]);

    // Like routes
    Route::apiResource('likes', LikeController::class)->only([
        'store',
        'destroy'
    ]);

    // Friend/Follow routes
    Route::apiResource('friends', FriendController::class)->only([
        'store',
        'update',
        'destroy'
    ]);
    
    // Check if following a user
    Route::get('/friends/check/{user}', [FriendController::class, 'checkFollowing']);

    // Report routes
    Route::post('/reports', [ReportController::class, 'store']);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('reports', ReportController::class)->except([
            'create',
            'edit',
            'store'
        ]);
    });
});