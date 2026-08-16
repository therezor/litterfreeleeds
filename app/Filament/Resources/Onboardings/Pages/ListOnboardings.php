<?php

namespace App\Filament\Resources\Onboardings\Pages;

use App\Filament\Resources\Onboardings\OnboardingResource;
use Filament\Resources\Pages\ListRecords;

class ListOnboardings extends ListRecords
{
    protected static string $resource = OnboardingResource::class;

    public function getTitle(): string
    {
        return 'Onboarding';
    }

    public function getSubheading(): ?string
    {
        return 'Volunteers matched to you. Get in touch, hand over purple bags, then mark them as onboarded.';
    }

    protected function getHeaderActions(): array
    {
        // Nothing to create — volunteers arrive by registering at /join.
        return [];
    }
}
