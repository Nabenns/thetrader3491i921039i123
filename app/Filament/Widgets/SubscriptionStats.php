<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = '30s';
    protected static bool $isLazy = true;
    protected static ?string $cacheKey = 'subscription_stats';
    protected static ?int $cacheTtl = 300; // 5 minutes

    protected function getStats(): array
    {
        return [
            Stat::make('Total Revenue', 'Rp ' . number_format(Transaction::where('status', 'paid')->sum('amount'), 0, ',', '.'))
                ->description('All time revenue')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Active Subscriptions', Subscription::where('status', 'active')->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', now()))->count())
                ->description('Current active users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('New Subscriptions (7 Days)', Subscription::where('created_at', '>=', now()->subDays(7))->count())
                ->description('Growth in last 7 days')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
