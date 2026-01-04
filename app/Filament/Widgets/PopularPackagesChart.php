<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PopularPackagesChart extends ChartWidget
{
    protected ?string $heading = 'Popular Packages';
    protected static ?int $sort = 3;
    protected static bool $isLazy = true;
    protected static ?string $cacheKey = 'popular_packages_chart';
    protected static ?int $cacheTtl = 300; // 5 minutes

    protected function getData(): array
    {
        $data = \App\Models\Subscription::query()
            ->join('packages', 'subscriptions.package_id', '=', 'packages.id')
            ->selectRaw('packages.name as package_name, count(*) as count')
            ->where('subscriptions.status', 'active')
            ->groupBy('packages.name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Subscriptions',
                    'data' => $data->pluck('count'),
                    'backgroundColor' => [
                        '#3b82f6', // blue-500
                        '#10b981', // emerald-500
                        '#f59e0b', // amber-500
                        '#ef4444', // red-500
                        '#8b5cf6', // violet-500
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $data->pluck('package_name'),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
