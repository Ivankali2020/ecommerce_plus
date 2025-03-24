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
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class StatsOverview extends BaseWidget
{
    // protected int | string | array $columnSpan = ['md' => 3, 'xl' => 3];
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
        // Get seller ID based on filters
        $sellerId = $this->admin_id ?? CheckFilamentSellerUser::getSellerId();
        
        // Create query for orders
        $orderQuery = Order::where('seller_id', $sellerId)->getOnlyConfirm();
        
        // Default to current year if not selected
        $year = $this->year ?? Carbon::now()->format('Y');
        
        // Apply date filters based on what's available
        if ($this->month !== null) {
            // If month is selected, filter by both year and month
            $startDate = Carbon::createFromDate($year, $this->month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $this->month, 1)->endOfMonth();
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
            
            $dateDescription = sprintf('%s (%s %s)', 
                __('filament/widgets/stats-overview.calculate_by_laks'),
                Carbon::createFromDate($year, $this->month, 1)->format('F'),
                $year
            );
        } elseif ($this->year !== null) {
            // If only year is selected, filter by entire year
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
            
            $dateDescription = sprintf('%s (%s)', 
                __('filament/widgets/stats-overview.calculate_by_laks'),
                $year
            );
        } else {
            // No filters, use default description
            $dateDescription = __('filament/widgets/stats-overview.calculate_by_laks');
        }
        
        // Get total revenue
        $totalOrderRevenues = $orderQuery->sum('total_price');
        
        return [
            Stat::make(__('filament/widgets/stats-overview.unique_products'), Product::where('seller_id', $sellerId)->count() . ' ' . __('filament/widgets/stats-overview.pcs'))
                ->description(__('filament/widgets/stats-overview.total_products')),

            Stat::make(__('filament/widgets/stats-overview.unique_customers'), User::where('seller_id', $sellerId)->count())
                ->description(__('filament/widgets/stats-overview.persons_all_time')),

            Stat::make(__('filament/widgets/stats-overview.total_revenue'), $totalOrderRevenues > 0 ? $totalOrderRevenues / 100000 : 0)
                ->description($dateDescription)
        ];
    }
}
