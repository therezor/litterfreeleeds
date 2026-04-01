<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CommunityLitterTypeChart extends ChartWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'half';

    protected ?string $heading = 'Litter Type Distribution';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Items Collected',
                    'data' => [45, 15, 12, 18, 10],
                    'backgroundColor' => ['#9333ea', '#f59e0b', '#ef4444', '#10b981', '#6366f1'],
                ],
            ],
            'labels' => ['Plastic', 'Metal', 'Glass', 'Paper', 'Other'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
