<?php

namespace App\Filament\Resources\CommunityPicks\Pages;

use App\Filament\Resources\CommunityPicks\CommunityPickResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCommunityPick extends EditRecord
{
    protected static string $resource = CommunityPickResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
