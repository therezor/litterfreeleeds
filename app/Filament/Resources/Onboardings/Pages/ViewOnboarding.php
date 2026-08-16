<?php

namespace App\Filament\Resources\Onboardings\Pages;

use App\Filament\Resources\Onboardings\OnboardingResource;
use App\Filament\Resources\Onboardings\Tables\OnboardingsTable;
use Filament\Resources\Pages\ViewRecord;

class ViewOnboarding extends ViewRecord
{
    protected static string $resource = OnboardingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            OnboardingsTable::onboardAction(),
        ];
    }
}
