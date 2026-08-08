<?php

namespace App\Filament\Resources\CommunityPicks\Pages;

use App\Filament\Resources\CommunityPicks\CommunityPickResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCommunityPick extends CreateRecord
{
    protected static string $resource = CommunityPickResource::class;
}
