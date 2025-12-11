<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:8|confirmed', 
        // ]);

        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);

        // $token = $user->createToken('api')->plainTextToken;

        // return response()->json([
        //     'user' => $user,
        //     'token' => $token,
        // ], 201);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return [
            'token' => $user->createToken('api')->plainTextToken,
        ];
    }

    public function login(Request $request)
    {
        // $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required',
        // ]);

        // if (!Auth::attempt($request->only('email', 'password'))) {
        //     throw ValidationException::withMessages([
        //         'email' => ['The provided credentials are incorrect.'],
        //     ]);
        // }

        // $user = User::where('email', $request->email)->firstOrFail();

        // $token = $user->createToken('auth_token')->plainTextToken;

        // return response()->json([
        //     'user' => $user,
        //     'token' => $token,
        // ]);
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            abort(401, 'Invalid credentials.');
        }

        return [
            'token' => $user->createToken('api')->plainTextToken,
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
