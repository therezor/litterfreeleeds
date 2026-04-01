<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PersonalActivityChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Your Monthly Collection';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Bags Collected',
                    'data' => [4, 10, 8, 12, 5, 15, 10],
                    'fill' => 'start',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}
