<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GeneralStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Community Statistics';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Community Bags', '12,450')
                ->description('8% increase from last month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([1000, 1500, 1200, 1800, 2000, 2500, 2450]),
            Stat::make('Active Local Groups', '52')
                ->description('3 new groups joined this month')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('purple'),
            Stat::make('Leeds "Purpled"', '68%')
                ->description('Percentage of city neighborhoods with active pickers')
                ->descriptionIcon('heroicon-m-map')
                ->color('success'),
        ];
    }
}
