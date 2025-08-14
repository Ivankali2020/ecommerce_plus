<?php

namespace App\Helper;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserHelper
{
    public static function updateSellerId($id)
    {
        if (Auth::guard('web')->check()) {
            DB::table('users')
                ->where('id', Auth::guard('web')->id())
                ->update(['seller_id' => $id]);
        }
    }

    public static function getUserSellerId()
    {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->seller_id ?? 1;
        }

        return 1;
    }
}
