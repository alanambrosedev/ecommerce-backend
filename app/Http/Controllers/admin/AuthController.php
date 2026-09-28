<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function authenticate(LoginRequest $request)
    {

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid email or password.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        if ($user->role !== 'admin') {
            Auth::logout();

            return response()->json([
                'message' => 'Unauthorized access.',
            ], Response::HTTP_FORBIDDEN);
        }

        $user->tokens()->delete();
        $token = $user->createToken('admin-auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], Response::HTTP_OK);
    }
}
