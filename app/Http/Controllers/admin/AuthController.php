<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function authenticate(LoginRequest $request)
    {
        $result = $this->authService->authenticate($request->only('email', 'password'), 'admin', 'admin-auth-token');
        return response()->json(
            $result['data'],
            $result['status'],
        );
    }
}
