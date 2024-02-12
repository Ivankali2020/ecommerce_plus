<?php

namespace App\Filament\Seller\Widgets;

use App\Helper\CheckFilamentSellerUser;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Region;
use Filament\Widgets\ChartWidget;

class BestSellerProduct extends ChartWidget
{
    protected static ?string $heading = 'Top Ten Best Seller';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $data = Product::where('seller_id',CheckFilamentSellerUser::getSellerId())
            ->withSum('orders','quantity')
            ->orderBy('orders_sum_quantity','desc')->limit(10)->get();

        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => $data->map(fn($r) => $r->orders_sum_quantity ?? 0),
                    'backgroundColor' => ['#edfcf3','#d4f7df','#9eeabb','#77dea5','#40c781','#1dac66','#108b52','#0d6f44','#0d5837','#0b492e'],
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $data->map(fn($r) => $r->name),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
