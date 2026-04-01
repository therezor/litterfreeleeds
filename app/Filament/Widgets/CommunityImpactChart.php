<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CommunityImpactChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'half';

    protected ?string $heading = 'Total Community Impact';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total Bags Collected',
                    'data' => [1200, 1500, 1100, 1800, 2100, 2500, 2300, 1900, 1700, 2200, 2400, 2600],
                    'backgroundColor' => '#9333ea',
                    'borderColor' => '#9333ea',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getColumns(): int|array
    {
        return 3;
    }
}
