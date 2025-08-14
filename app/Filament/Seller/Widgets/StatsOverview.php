<?php

namespace App\Filament\Seller\Widgets;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Helper\UserHelper;
use App\Helper\OrderStatus;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Helper\CheckFilamentSellerUser;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Seller\Resources\OrderResource\Widgets\OrderChart;
use App\Models\Variant;
use App\Models\Expanse;

class StatsOverview extends BaseWidget
{
    // protected int | string | array $columnSpan = 3;

    protected static ?string $pollingInterval = null;


    // Add filter properties
    public $admin_id = null;

    public $year = null;

    public $month = null;

    public function mount(): void
    {
        // $this->year = Carbon::now()->format('Y');
        // $this->month = Carbon::now()->format('n');
    }

    // Update filter methods with Livewire 3 attribute-based listeners
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

    protected function getStats(): array
    {
        $sellerId = $this->admin_id ?? CheckFilamentSellerUser::getSellerId();
        $year = $this->year ?? Carbon::now()->format('Y');

        [$startDate, $endDate, $dateDescription] = $this->getDateFilters($year);

        // Calculate revenue and profit from orders
        $orderQuery = Order::where('seller_id', $sellerId)
            ->where('status', OrderStatus::Delivered->value);
        if ($startDate && $endDate) {
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $totalRevenue = $orderQuery->sum('total_price');
        $filteredOrderIds = $orderQuery->pluck('id');

        // Calculate profits and capital
        $totalProfit = \App\Models\OrderProduct::whereIn('order_id', $filteredOrderIds)->sum('profit');
        $soldProductsCapital = \App\Models\OrderProduct::whereIn('order_id', $filteredOrderIds)
            ->selectRaw('SUM(original_buy_price * quantity) as total')
            ->value('total');

        $inventoryCapital = $this->calculateInventoryCapital($sellerId);

        // Calculate expenses
        $totalExpenses = $this->calculateExpenses($sellerId, $startDate, $endDate);
        $finalProfit = $totalProfit - $totalExpenses;

        return [
            Stat::make(__('filament/widgets/stats-overview.unique_products'), Product::where('seller_id', $sellerId)->count() . ' ' . __('filament/widgets/stats-overview.pcs'))
                ->description(__('filament/widgets/stats-overview.total_products'))
                ->icon('heroicon-o-cube'),

            Stat::make(__('filament/widgets/stats-overview.total_amount'), number_format($soldProductsCapital + $inventoryCapital, 0))
                ->description(__('filament/widgets/stats-overview.total_amount'))
                ->icon('heroicon-o-banknotes'),

            Stat::make(__('filament/widgets/stats-overview.total_revenue'), $totalRevenue > 0 ? number_format($totalRevenue, 0) : 0)
                ->description($dateDescription)
                ->icon('heroicon-o-chart-bar'),

            Stat::make(__('filament/widgets/stats-overview.profit_loss'), number_format($totalProfit, 0))
                ->description($dateDescription)
                ->icon('heroicon-o-arrow-trending-up')
                ->color($totalProfit >= 0 ? 'success' : 'danger'),

            Stat::make(__('filament/widgets/stats-overview.total_expenses'), number_format($totalExpenses, 0))
                ->description($dateDescription)
                ->icon('heroicon-o-minus-circle')
                ->color('warning'),

            Stat::make(__('filament/widgets/stats-overview.final_profit'), number_format($finalProfit, 0))
                ->description($dateDescription)
                ->icon($finalProfit >= 0 ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                ->color($finalProfit >= 0 ? 'success' : 'danger')
        ];
    }

    /**
     * Get date filters and description based on selected year/month
     */
    private function getDateFilters($year): array
    {
        if ($this->month !== null) {
            $startDate = Carbon::createFromDate($year, $this->month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $this->month, 1)->endOfMonth();
            $dateDescription = sprintf(
                '%s (%s %s)',
                __('filament/widgets/stats-overview.calculate_by_laks'),
                Carbon::createFromDate($year, $this->month, 1)->format('F'),
                $year
            );
        } elseif ($this->year !== null) {
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
            $dateDescription = sprintf(
                '%s (%s)',
                __('filament/widgets/stats-overview.calculate_by_laks'),
                $year
            );
        } else {
            $startDate = null;
            $endDate = null;
            $dateDescription = __('filament/widgets/stats-overview.calculate_by_laks');
        }

        return [$startDate, $endDate, $dateDescription];
    }

    /**
     * Calculate total inventory capital from products and variants
     */
    private function calculateInventoryCapital($sellerId): float
    {
        $productCapital = Product::where('seller_id', $sellerId)
            ->selectRaw('SUM(mmk_buy_price * quantity) as total')
            ->value('total') ?? 0;

        $variantCapital = Variant::selectRaw('SUM(mmk_buy_price * quantity) as total')
            ->value('total') ?? 0;

        return $productCapital + $variantCapital;
    }

    /**
     * Calculate total expenses for the filtered period
     */
    private function calculateExpenses($sellerId, $startDate, $endDate): float
    {
        $query = Expanse::where('seller_id', $sellerId);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->sum('amount') ?? 0;
    }
}
