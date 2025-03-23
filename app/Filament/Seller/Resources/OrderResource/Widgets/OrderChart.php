<?php

namespace App\Filament\Seller\Resources\OrderResource\Widgets;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OrderChart extends ChartWidget
{
    public function getHeading(): string 
    {
        return __('filament/widgets/order-chart.heading');
    }

    public function getDescription(): ?string
    {
        return __('filament/widgets/order-chart.description');
    }

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        switch ($activeFilter) {
            case ('all') :
                $data = Trend::query(Order::where('seller_id',CheckFilamentSellerUser::getSellerId()))
                    ->between(
                        start: now()->subYears(6)->startOfYear(),
                        end: now()->endOfYear(),
                    )
                    ->perYear()
                    ->sum('total_price');

                return [
                    'datasets' => [
                        [
                            'label' => __('filament/widgets/order-chart.total_order_amount'),
                            'data' => $data->map(fn(TrendValue $value)=> number_format($value->aggregate / 1000,2)) ,
                        ],
                    ],
                    'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('Y')),
                ];

            case ('week') :
                $data = Trend::query(Order::where('seller_id',CheckFilamentSellerUser::getSellerId()))
                    ->between(
                        start: now()->startOfWeek(),
                        end: now()->endOfWeek(),
                    )
                    ->perDay()
                    ->sum('total_price');

                return [
                    'datasets' => [
                        [
                            'label' => __('filament/widgets/order-chart.total_order_amount'),
                            'data' => $data->map(fn(TrendValue $value)=> number_format($value->aggregate / 1000,2)) ,
                        ],
                    ],
                    'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d M ')),
                ];

            case ('year') :
                $data = Trend::query(Order::where('seller_id',CheckFilamentSellerUser::getSellerId()))
                    ->between(
                        start: now()->startOfYear(),
                        end: now()->endOfYear(),
                    )
                    ->perMonth()
                    ->sum('total_price');

                return [
                    'datasets' => [
                        [
                            'label' => __('filament/widgets/order-chart.total_order_amount'),
                            'data' => $data->map(fn(TrendValue $value)=> number_format($value->aggregate / 1000,2)) ,
                        ],
                    ],
                    'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M')),
                ];

            default:
                $data = Trend::query(Order::where('seller_id',CheckFilamentSellerUser::getSellerId()))
                    ->between(
                        start: now()->startOfMonth(),
                        end: now()->endOfMonth(),
                    )
                    ->perDay()
                    ->sum('total_price');

                return [
                    'datasets' => [
                        [
                            'label' => __('filament/widgets/order-chart.total_order_amount'),
                            'data' => $data->map(fn(TrendValue $value)=> $value->aggregate / 1000) ,
                        ],
                    ],
                    'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d M')),
                ];
        }

    }

   protected function getOptions(): array|RawJs|null
   {
       return RawJs::make(<<<JS
        {
            scales: {
                y: {
                    ticks: {
                            callback: (value) =>  value+ ' K',
                    },
                },
            },
        }
    JS);
   }

    protected function getFilters(): ?array
    {
        return [
            'month' => __('filament/widgets/order-chart.filters.month'),
            'year' => __('filament/widgets/order-chart.filters.year'),
            'week' => __('filament/widgets/order-chart.filters.week'),
            'all' => __('filament/widgets/order-chart.filters.all'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
