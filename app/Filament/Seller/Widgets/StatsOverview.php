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
    
    // protected int | string | array $columnSpan = ['md' => 3, 'xl' => 3];

    protected function getStats(): array
    {
        $totalOrderRevenues = Order::where('seller_id',CheckFilamentSellerUser::getSellerId())->getOnlyConfirm()->sum('total_price');
        return [
            Stat::make(__('filament/widgets/stats-overview.unique_products'), Product::where('seller_id',CheckFilamentSellerUser::getSellerId())->count() . ' ' . __('filament/widgets/stats-overview.pcs'))
                ->description(__('filament/widgets/stats-overview.total_products')),

            Stat::make(__('filament/widgets/stats-overview.unique_customers'), User::where('seller_id',CheckFilamentSellerUser::getSellerId())->count())
                ->description(__('filament/widgets/stats-overview.persons_all_time')),

            Stat::make(__('filament/widgets/stats-overview.total_revenue'), $totalOrderRevenues > 0 ? $totalOrderRevenues / 100000 : 0)
                ->description(__('filament/widgets/stats-overview.calculate_by_laks'))
        ];
    }
}
