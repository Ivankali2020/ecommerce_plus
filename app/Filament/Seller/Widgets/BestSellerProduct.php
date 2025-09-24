<?php

namespace App\Filament\Seller\Widgets;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Product;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class BestSellerProduct extends ChartWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = '1';

    // Add filter properties
    public $admin_id = null;

    public $year = null;

    public $month = null;

    public function getHeading(): string
    {
        return __('filament/widgets/order-by-region.best_seller');
    }

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    public function mount(): void
    {
        // $this->year = Carbon::now()->format('Y');
        // $this->month = Carbon::now()->format('n');
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
        $year = $this->year ?? Carbon::now()->format('Y');

        // Start with base query
        $query = Product::where('seller_id', $sellerId);

        // Apply date filters to the relationship
        if ($this->month !== null) {
            // If month is selected, filter by both year and month
            $startDate = Carbon::createFromDate($year, $this->month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $this->month, 1)->endOfMonth();

            $query->withSum(['orders' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }], 'quantity');
        } elseif ($this->year !== null) {
            // If only year is selected, filter by entire year
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();

            $query->withSum(['orders' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }], 'quantity');
        } else {
            // No date filters
            $query->withSum('orders', 'quantity');
        }

        // Get top 10 products
        $data = $query->orderBy('orders_sum_quantity', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament/widgets/order-by-region.best_seller'),
                    'data' => $data->map(fn ($r) => $r->orders_sum_quantity ?? 0),
                    'backgroundColor' => ['#edfcf3', '#d4f7df', '#9eeabb', '#77dea5', '#40c781', '#1dac66', '#108b52', '#0d6f44', '#0d5837', '#0b492e'],
                    'borderColor' => ['#edfcf3', '#d4f7df', '#9eeabb', '#77dea5', '#40c781', '#1dac66', '#108b52', '#0d6f44', '#0d5837', '#0b492e'],
                ],
            ],
            'labels' => $data->map(fn ($r) => $r->name),
        ];
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<'JS'
        {
            indexAxis: 'y',
        }
        JS);
    }

    protected function getYAxisLabel(): string
    {
        return __('filament/widgets/order-by-region.quantity_sold');
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
