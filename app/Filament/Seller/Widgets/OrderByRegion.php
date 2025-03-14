<?php

namespace App\Filament\Seller\Widgets;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Region;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;

class OrderByRegion extends ChartWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 2;

    // protected static ?string $maxHeight = '350px';

    public function getHeading(): string
    {
        return __('filament/widgets/order-by-region.heading');
    }

    protected function getData(): array
    {
        $data = Region::withSum(['orders' => function($q){
            return $q->where('seller_id',CheckFilamentSellerUser::getSellerId())->getOnlyConfirm();
        }],'total_price')->get();
        return [
               'datasets' => [
                   [
                       'label' => __('filament/widgets/order-by-region.revenue_label'),
                       'data' => $data->map(fn($r) => $r->orders_sum_total_price ?? 0),
                       'backgroundColor' => [
                           '#4e79a7', '#f28e2c', '#e15759', '#76b7b2', 
                           '#59a14f', '#edc949', '#af7aa1', '#ff9da7', 
                           '#9c755f', '#bab0ab', '#5f4690', '#1d91c0'
                       ],
                       'borderColor' => '#9BD0F5',
                   ],
               ],
               'labels' => $data->map(fn($r) => $r->mm_name),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<JS
        {
            indexAxis: 'y',
        }
        JS);
    }

}
