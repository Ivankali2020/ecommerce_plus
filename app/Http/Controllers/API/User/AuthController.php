<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ResponseTraits;

class AuthController extends Controller
{
    use ResponseTraits;

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request, $sellerId)
    {
        $token = $this->authService->register($request->validated(), $sellerId);

        return $this->success('Create new account successful', [
            'token' => $token,
        ]);
    }

    public function login(LoginRequest $request, $sellerId)
    {
        $token = $this->authService->login($request->validated(), $sellerId);

        return $this->success('Login successful', [
            'token' => $token,
        ]);
    }

    public function logout()
    {
        $this->authService->logout();

        return $this->success('Logout successful');
    }
}
