<?php

namespace App\Services;

use App\Exceptions\DeleteDataFailException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserAccountService
{
    /**
     * Get Account
     *
     * @return User $user
     */
    public function getMyAccount(): User
    {
        $user = Auth::guard('user')->user();

        return $user;
    }

    /**
     * Account Delete
     */
    public function deleteAccount(): bool
    {
        try {
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $user->delete();

            return true;
        } catch (Throwable $th) {
            throw new DeleteDataFailException($th->getMessage());
        }
    }
}
