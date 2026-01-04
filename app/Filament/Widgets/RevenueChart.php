<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (Last 30 Days)';
    protected static ?int $sort = 2;
    protected ?string $pollingInterval = null;
    protected static bool $isLazy = true;
    protected static ?string $cacheKey = 'revenue_chart';
    protected static ?int $cacheTtl = 300; // 5 minutes

    protected function getData(): array
    {
        $data = Trend::query(Transaction::where('status', 'paid'))
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#10b981', // Emerald 500
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
