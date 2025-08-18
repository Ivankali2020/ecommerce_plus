<?php

namespace App\Policies;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * Admins can access all tenant data from the admin panel.
     */
    public function before(FilamentUser $user, $ability)
    {
        // Allow admins to access all tenant data from admin panel
        if (auth()->guard('admin')->check()) {
            return true;
        }

        return null; // Continue with normal authorization
    }
}
