<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Usuario;
use App\Services\Factories\ValidationServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $usuario = Usuario::where('usua_email', $request->email)->first();
        $user = User::where('email', $request->email)->first();

        $dash = ValidationServiceFactory::create('dash');

        if (! $usuario || ! $dash->validate($usuario)) {
            return response()->json(null, Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'active' => $usuario->active,
            'token' => explode('|', $token)[1],
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(null, Response::HTTP_OK);
    }
}
