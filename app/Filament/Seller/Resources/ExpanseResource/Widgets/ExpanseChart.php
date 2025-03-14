<?php

namespace App\Filament\Seller\Resources\ExpanseResource\Widgets;

use App\Models\Expanse;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class ExpanseChart extends ChartWidget
{
    protected static ?string $heading = null;
    
    public null | string $filter = 'year';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 'full';
    
    public function getHeading(): string
    {
        return __('filament/widgets/expanse-chart.heading');
    }
    
    protected function getFilters(): ?array
    {
        return [
            'month' => __('filament/widgets/expanse-chart.filters.month'),
            'year' => __('filament/widgets/expanse-chart.filters.year'),
            'all' => __('filament/widgets/expanse-chart.filters.all'),
        ];
    }

    protected function getData(): array
    {
        $data = $this->generateChartData();
        
        return [
            'datasets' => [
                [
                    'label' => __('filament/widgets/expanse-chart.expenses_label'),
                    'data' => $data['values'],
                ],
            ],
            'labels' => $data['labels'],
        ];
    }
    
    protected function generateChartData(): array
    {
        if ($this->filter === 'month') {
            // Current month data (daily)
            $data = Trend::model(Expanse::class)
                ->between(
                    start: now()->startOfMonth(),
                    end: now()->endOfMonth(),
                )
                ->perDay()
                ->sum('amount');
                
            return [
                'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d')),
                'values' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ];
            
        } elseif ($this->filter === 'year') {
            // Current year data (monthly)
            $data = Trend::model(Expanse::class)
                ->between(
                    start: now()->startOfYear(),
                    end: now()->endOfYear(),
                )
                ->perMonth()
                ->sum('amount');
                
            return [
                'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M')),
                'values' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ];
            
        } else {
            // All time data (yearly)
            $data = Trend::model(Expanse::class)
                ->between(
                    start: now()->subYears(4)->startOfYear(),
                    end: now()->endOfYear(),
                )
                ->perYear()
                ->sum('amount');
                
            return [
                'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('Y')),
                'values' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ];
        }
    }

    protected function getType(): string
    {
        return 'line';
    }
}
