<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    public function authenticate(array $credentials, string $requiredRole, string $tokenName): array
    {
        if (! Auth::attempt($credentials)) {
            return [
                'status' => Response::HTTP_UNAUTHORIZED,
                'data' => ['message' => 'Invalid email or password.'],
            ];
        }

        $user = Auth::user();

        if ($user->role !== $requiredRole) {
            Auth::logout();

            return [
                'status' => Response::HTTP_FORBIDDEN,
                'data' => ['message' => 'Unauthorized access.'],
            ];
        }

        $user->tokens()->delete();
        $token = $user->createToken($tokenName)->plainTextToken;

        return [
            'status' => Response::HTTP_OK,
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],

            ],
        ];
    }
}
