<?php

namespace App\Filament\Seller\Widgets;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Region;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;

class OrderByRegion extends ChartWidget
{
    protected static ?string $heading = 'Revenue By Region';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $data = Region::withSum(['orders' => function($q){
            return $q->where('seller_id',CheckFilamentSellerUser::getSellerId())->getOnlyConfirm();
        }],'total_price')->get();
        return [
               'datasets' => [
                   [
                       'label' => 'Blog posts created',
                       'data' => $data->map(fn($r) => $r->orders_sum_total_price ?? 0),
                       'backgroundColor' => ['#edfcf3','#d4f7df','#9eeabb','#77dea5','#40c781','#1dac66','#108b52','#0d6f44','#0d5837','#0b492e','#05291a','#26acb4','#238b97','#22707c','#245c66'],
                       'borderColor' => '#9BD0F5',
                   ],
               ],
               'labels' => $data->map(fn($r) => $r->mm_name),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
            ],
        ],
    ];
}
