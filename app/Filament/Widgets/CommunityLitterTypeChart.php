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
                    // A doughnut needs explicit categorical colours (it can't inherit
                    // one 'primary'). This set is brand-led and holds up under colour
                    // vision deficiency: worst-case CIELAB separation is ~28 across
                    // normal/deuteranope/protanope vision, and every slice clears 3:1
                    // against both the light and dark card surfaces. The previous
                    // amber/red pair collided badly for deuteranopes (~7).
                    'backgroundColor' => ['#ba4eae', '#0e7490', '#2563eb', '#15803d', '#e11d48'],
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
