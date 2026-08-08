<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PersonalWeekdayChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'half';

    protected ?string $heading = 'Activity by Weekday';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Bags Collected',
                    // See CommunityImpactChart: inherits the panel's 'primary' colour.
                    'data' => [2, 1, 0, 1, 3, 8, 12],
                ],
            ],
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
