<?php

namespace App\Filament\Seller\Widgets;

use App\Filament\Seller\Resources\OrderResource\Widgets\OrderChart;
use App\Helper\CheckFilamentSellerUser;
use App\Helper\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected function getStats(): array
    {
        $totalOrderRevenues = Order::where('seller_id',CheckFilamentSellerUser::getSellerId())->getOnlyConfirm()->sum('total_price');
        return [
            Stat::make('Unique products', Product::where('seller_id',CheckFilamentSellerUser::getSellerId())->count() . ' pcs')
                ->description('your total products'),

            Stat::make('Unique customers', User::where('seller_id',CheckFilamentSellerUser::getSellerId())->count())
                ->description('persons of all time'),

            Stat::make('Total Revenue', $totalOrderRevenues > 0 ? $totalOrderRevenues / 100000 : 0)
                ->description('calculate by laks')
        ];
    }
}
