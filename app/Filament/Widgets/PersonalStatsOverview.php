<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PersonalStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Your Statistics';

    protected function getStats(): array
    {
        return [
            Stat::make('Your Bags Collected', '42')
                ->description('12% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            Stat::make('Weekly Streak', '12 Weeks')
                ->description('Keep it up! Next goal: 15 weeks')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),
            Stat::make('Community Hero Rank', '#14')
                ->description('Top 5% of volunteers')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
}
