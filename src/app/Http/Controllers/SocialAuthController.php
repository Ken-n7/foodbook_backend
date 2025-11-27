<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    // Step 1: Redirect to provider (Google)
    public function redirect($provider)
    {
        // $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        // dd($url);

        return Socialite::driver($provider)->stateless()->redirect();
    }

    // Step 2: Handle provider callback
    public function callback($provider)
    {
        // Log::info('Callback received', [
        //     'all_params' => request()->all(),
        //     'has_code' => request()->has('code'),
        //     'code' => request()->get('code'),
        // ]);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            // Log or handle error
            return response()->json(['error' => 'Invalid OAuth callback: ' . $e->getMessage()], 400);
        }

        // Now find or create user safely
        $user = User::firstOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'name' => $socialUser->getName(),
            'password' => bcrypt(Str::random(12)),
            'provider_id' => $socialUser->getId(),
            'provider_name' => $provider,
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        // Redirect with token
        // return redirect()->away(env('ANGULAR_REDIRECT_URL') . '?token=' . $token);
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
