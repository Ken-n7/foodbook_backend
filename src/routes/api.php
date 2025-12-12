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

Route::post('/posts/{post}/toggle-like', [PostController::class, 'toggleLike'])
    ->middleware('auth:sanctum');
// Routes protected by Sanctum auth
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/user', [AuthController::class, 'user']);


    // Current user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // User resource routes (only show, update, delete)
    Route::resource('users', UserController::class)->only([
        'show',
        'update',
        'destroy'
    ]);

    Route::apiResource('restaurants', RestaurantController::class);
    Route::apiResource('posts', PostController::class)->except('toggleLike');

    Route::apiResource('ratings', RatingController::class)->only([
        'store',
        'destroy'
    ]);

    Route::apiResource('comments', CommentController::class)->only([
        'store',
        'destroy'
    ]);

    Route::apiResource('likes', LikeController::class)->only([
        'store',
        'destroy'
    ]);

    Route::apiResource('friends', FriendController::class)->only([
        'store',
        'update',
        'destroy'
    ]);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('reports', ReportController::class)->except([
            'create',
            'edit'
        ]);
    });

    Route::post('/reports', [ReportController::class, 'store']);
});
