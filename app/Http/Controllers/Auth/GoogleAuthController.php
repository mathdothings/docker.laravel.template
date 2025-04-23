<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function handleGoogleLogin(Request $request)
    {
        try {
            $accessToken = $request->validate([
                'access_token' => ['required', 'string', 'max:300'],
            ]);

            $googleUser = Socialite::driver('google')
                ->scopes(['email'])
                ->userFromToken($accessToken['access_token']);

            return response()->json([
                'email' => $googleUser->getEmail()], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
