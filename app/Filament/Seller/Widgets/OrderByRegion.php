<?php

namespace App\Filament\Seller\Widgets;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Region;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class OrderByRegion extends ChartWidget
{
    protected ?string $heading = null;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = '1';

    // Add filter properties
    public $admin_id = null;

    public $year = null;

    public $month = null;

    protected ?string $pollingInterval = null;

    public function getHeading(): string
    {
        return __('filament/widgets/order-by-region.heading');
    }

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
        $year = $this->year ?? Carbon::now()->format('Y');

        // Build query with filters
        $query = Region::withSum(['orders' => function ($q) use ($sellerId, $year) {
            $q = $q->where('orders.seller_id', $sellerId)->getOnlyConfirm();

            // Apply date filters based on what's available
            if ($this->month !== null) {
                // If month is selected, filter by both year and month
                $startDate = Carbon::createFromDate($year, $this->month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $this->month, 1)->endOfMonth();
                $q->whereBetween('orders.created_at', [$startDate, $endDate]);
            } elseif ($this->year !== null) {
                // If only year is selected, filter by entire year
                $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
                $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
                $q->whereBetween('orders.created_at', [$startDate, $endDate]);
            }

            return $q;
        }], 'total_price');

        $data = $query->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament/widgets/order-by-region.revenue_label'),
                    'data' => $data->map(fn ($r) => $r->orders_sum_total_price ?? 0),
                    'backgroundColor' => [
                        '#edc949',
                    ],
                    'borderColor' => '#edc949',
                ],
            ],
            'labels' => $data->map(fn ($r) => $r->mm_name),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<'JS'
        {
            indexAxis: 'y',
        }
        JS);
    }
}
