<?php

namespace App\Filament\Resources\CommunityPicks\Pages;

use App\Filament\Resources\CommunityPicks\CommunityPickResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommunityPicks extends ListRecords
{
    protected static string $resource = CommunityPickResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
