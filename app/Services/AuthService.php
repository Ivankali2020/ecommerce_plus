<?php

namespace App\Services;

use App\Exceptions\RegistrationFailException;
use App\Exceptions\ResourceForbiddenException;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthService
{
    /**
     * User Register
     *
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
        } catch (Throwable $th) {
            DB::rollBack();
            throw new RegistrationFailException($th->getMessage());
        }
    }

    /**
     * User Login
     *
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
            throw new AuthenticationException;
        }

        if (! Hash::check($loginData['password'], $user->password)) {
            throw new ResourceForbiddenException('Invalid email or password');
        }

        return $user->createToken('user-token')->plainTextToken;
    }

    /**
     * User Logout
     */
    public function logout()
    {
        /** @var User $user * */
        $user = Auth::guard('user')->user();
        $user->tokens()->delete();
    }
}
