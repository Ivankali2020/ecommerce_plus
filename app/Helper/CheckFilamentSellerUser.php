<?php

namespace App\Helper;

use Filament\Facades\Filament;

class CheckFilamentSellerUser
{

    public static function getSellerId()
    {
        if (Filament::getAuthGuard() == 'seller'){
           return Filament::auth()->user()->parent_id == null ? Filament::auth()->id() : Filament::auth()->user()->parent_id;
        }

    }

    public static function checkAdminSuperUser()
    {
        if (Filament::getAuthGuard() == 'admin'){
            return Filament::auth()->user()->roles()->where('name','super_admin')->exists();
        }

    }

}
