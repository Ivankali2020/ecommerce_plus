<?php

namespace App\Services;

use App\Exceptions\RegistrationFailException;
use App\Exceptions\ResourceForbiddenException;
use App\Exceptions\UnprocessableRequestException;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\WelcomeNotificaton;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class AuthService
{
    /**
     * User Register
     *
     * @param array $userData
     * @return string token
     */
    public function register(array $userData, int $sellerId): string
    {
        $seller = Seller::findOrFail($sellerId);

        DB::beginTransaction();
        try {
            $user = $seller->users()->create($userData);
            $token = $user->createToken('user-token')->plainTextToken;
            DB::commit();
            return $token;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new RegistrationFailException($th->getMessage());
        }
    }

    /**
     * User Login
     *
     * @param array $loginData
     * @return string token
     */
    public function login(array $loginData, $sellerId)
    {

        $seller = Seller::findOrFail($sellerId);
        $user = User::where('email', $loginData['email'])->first();

        if (is_null($user)) {
            throw new ResourceForbiddenException('Invalid email or password');
        }

        if ($user->seller->id !== $seller->id) {
            throw new AuthenticationException();
        }

        if (!Hash::check($loginData['password'], $user->password)) {
            throw new ResourceForbiddenException('Invalid email or password');
        }

        return $user->createToken('user-token')->plainTextToken;
    }

    /**
     * User Logout
     */
    public function logout()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::guard('user')->user();
        $user->tokens()->delete();
    }
}
