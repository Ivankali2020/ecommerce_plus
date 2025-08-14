<?php

namespace App\Helper;

use Filament\Facades\Filament;

class CheckFilamentSellerUser
{
    public static function getSellerId()
    {
        if (Filament::getAuthGuard() == 'seller') { // null ဆို ရင် seller ဖြစ်ပါတယ်
            return Filament::auth()->user()->parent_id == null ? Filament::auth()->id() : Filament::auth()->user()->parent_id;
        }

        return null;
    }

    public static function getStaffId()
    {
        // ဒါက ၀န်ထမ်းက order လုပ်ရင် သူ့ရဲ့ staff_id ကို သိမ်းမှာ ဖြစ်ပါတယ်
        if (Filament::getAuthGuard() == 'seller') { // null ဆို ရင် seller ဖြစ်ပါတယ်
            return Filament::auth()->user()->parent_id != null ? Filament::auth()->id() : null;
        }

        return null;
    }

    public static function checkAdminSuperUser()
    {
        if (Filament::getAuthGuard() == 'admin') {
            return Filament::auth()->user()->roles()->where('name', 'super_admin')->exists();
        }

        return null;
    }
}
