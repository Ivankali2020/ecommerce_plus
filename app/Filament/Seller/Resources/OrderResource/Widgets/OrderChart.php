<?php

namespace App\Filament\Seller\Resources\OrderResource\Widgets;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Livewire\Attributes\On;

class OrderChart extends ChartWidget
{
    // Add filter properties
    public $admin_id = null;
    public $year = null;
    public $month = null;
    
    public function getHeading(): string 
    {
        return __('filament/widgets/order-chart.heading');
    }

    public function getDescription(): ?string
    {
        return __('filament/widgets/order-chart.description');
    }

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';
    
    public function mount(): void
    {
        $this->year = Carbon::now()->format('Y');
        $this->month = Carbon::now()->format('n');
    }
    
    // Add filter event listeners
    #[On('updateAdminId')]
    public function updateAdminId(?string $admin_id): void
    {
        $this->admin_id = $admin_id;
    }
    
    #[On('updateYear')]
    public function updateYear(?string $year): void
    {
        $this->year = $year;
    }
    
    #[On('updateMonth')]
    public function updateMonth(?string $month): void
    {
        $this->month = $month;
    }

    protected function getData(): array
    {
        // Get seller ID based on filters
        $sellerId = $this->admin_id ?? CheckFilamentSellerUser::getSellerId();
        
        // Default to current year if not selected
        $selectedYear = $this->year ?? Carbon::now()->format('Y');
        $selectedMonth = $this->month;
        
        $activeFilter = $this->filter;
        switch ($activeFilter) {
            case ('all') :
                $data = Trend::query(Order::where('seller_id', $sellerId))
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
                            'data' => $data->map(fn(TrendValue $value)=> number_format($value->aggregate / 10000,2)) ,
                        ],
                    ],
                    'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('Y')),
                ];

            case ('week') :
                // For week view, use the selected year and month if available
                $startDate = $selectedMonth 
                    ? Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()->startOfWeek()
                    : now()->startOfWeek();
                $endDate = $selectedMonth 
                    ? Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfWeek()
                    : now()->endOfWeek();
                
                $data = Trend::query(Order::where('seller_id', $sellerId))
                    ->between(
                        start: $startDate,
                        end: $endDate,
                    )
                    ->perDay()
                    ->sum('total_price');

                return [
                    'datasets' => [
                        [
                            'label' => __('filament/widgets/order-chart.total_order_amount'),
                            'data' => $data->map(fn(TrendValue $value)=> number_format($value->aggregate / 10000,2)) ,
                        ],
                    ],
                    'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d M ')),
                ];

            case ('year') :
                // For year view, use the selected year
                $data = Trend::query(Order::where('seller_id', $sellerId))
                    ->between(
                        start: Carbon::createFromDate($selectedYear, 1, 1)->startOfYear(),
                        end: Carbon::createFromDate($selectedYear, 12, 31)->endOfYear(),
                    )
                    ->perMonth()
                    ->sum('total_price');

                return [
                    'datasets' => [
                        [
                            'label' => __('filament/widgets/order-chart.total_order_amount'),
                            'data' => $data->map(fn(TrendValue $value)=> number_format($value->aggregate / 10000,2)) ,
                        ],
                    ],
                    'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M')),
                ];

            default:
                // For month view (default), use the selected year and month
                $startDate = $selectedMonth 
                    ? Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()
                    : Carbon::createFromDate($selectedYear, 1, 1)->startOfMonth();
                $endDate = $selectedMonth 
                    ? Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()
                    : Carbon::createFromDate($selectedYear, 12, 31)->endOfMonth();
                
                $data = Trend::query(Order::where('seller_id', $sellerId))
                    ->between(
                        start: $startDate,
                        end: $endDate,
                    )
                    ->perDay()
                    ->sum('total_price');

                return [
                    'datasets' => [
                        [
                            'label' => __('filament/widgets/order-chart.total_order_amount'),
                            'data' => $data->map(fn(TrendValue $value)=> $value->aggregate / 10000) ,
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
