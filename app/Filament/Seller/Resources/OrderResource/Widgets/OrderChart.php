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
    protected static ?string $heading = 'Monthly Order Chart';

    protected int | string | array $columnSpan = '2';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        switch ($activeFilter) {
            case ('today') :
                $data = Trend::query(Order::where('seller_id',CheckFilamentSellerUser::getSellerId()))
                    ->between(
                        start: now()->startOfDay(),
                        end: now()->endOfDay(),
                    )
                    ->perHour()
                    ->sum('total_price');

                return [
                    'datasets' => [
                        [
                            'label' => 'Total Order Amount',
                            'data' => $data->map(fn(TrendValue $value)=> number_format($value->aggregate / 1000,2)) ,
                        ],
                    ],
                    'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('H:i A')),
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
                            'label' => 'Total Order Amount',
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
                            'label' => 'Total Order Amount',
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
                            'label' => 'Total Order Amount',
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
            'month' => 'This month',
            'year' => 'This year',
            'week' => 'Last week',
            'today' => 'Today',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
