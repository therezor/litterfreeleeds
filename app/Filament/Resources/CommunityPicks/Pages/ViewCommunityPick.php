<?php

namespace App\Filament\Resources\CommunityPicks\Pages;

use App\Filament\Resources\CommunityPicks\CommunityPickResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCommunityPick extends ViewRecord
{
    protected static string $resource = CommunityPickResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
