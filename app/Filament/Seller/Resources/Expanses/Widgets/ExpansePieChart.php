<?php

namespace App\Filament\Seller\Resources\Expanses\Widgets;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Expanse;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class ExpansePieChart extends ChartWidget
{
    protected ?string $heading = 'Expanse Pie Chart';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('filament/widgets/expanse-pie-chart.heading');
    }

    protected function getData(): array
    {
        $data = $this->generateChartData();

        return [
            'datasets' => [
                [
                    'label' => __('filament/widgets/expanse-pie-chart.expenses_label'),
                    'data' => $data['values'],
                    'backgroundColor' => $data['colors'],
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function generateChartData(): array
    {
        $sellerId = CheckFilamentSellerUser::getSellerId();

        $expansesByCategory = Expanse::where('seller_id', $sellerId)
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn (Collection $group) => $group->sum('amount'))
            ->sortByDesc('values')
            ->all();

        $totalExpenses = array_sum($expansesByCategory);

        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#FF6384',
            '#C9CBCF',
            '#4BC0C0',
            '#FF6384',
        ];

        $labels = array_map(
            fn (string $category, float $amount) => $this->formatLabelWithPercentage($category, $amount, $totalExpenses),
            array_keys($expansesByCategory),
            array_values($expansesByCategory)
        );

        return [
            'labels' => $labels,
            'values' => array_values($expansesByCategory),
            'colors' => array_slice($colors, 0, count($expansesByCategory)),
        ];
    }

    protected function formatLabelWithPercentage(string $category, float $amount, float $total): string
    {
        if ($total === 0) {
            return $category;
        }

        $percentage = ($amount / $total) * 100;

        return sprintf('%s (%.1f%%)', $category, $percentage);
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
