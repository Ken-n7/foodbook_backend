<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


// Route::post('/register', function (Request $request) {
//     $user = User::create([
//         'name' => $request->name,
//         'email' => $request->email,
//         'password' => Hash::make($request->password),
//     ]);

//     return [
//         'token' => $user->createToken('api')->plainTextToken,
//     ];
// });

// Route::post('/login', function (Request $request) {
//     $user = User::where('email', $request->email)->first();

//     if (! $user || ! Hash::check($request->password, $user->password)) {
//         abort(401, 'Invalid credentials.');
//     }

//     return [
//         'token' => $user->createToken('api')->plainTextToken,
//     ];
// });

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });