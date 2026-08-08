<?php

namespace App\Filament\Resources\CommunityPicks;

use App\Filament\Resources\CommunityPicks\Pages\CreateCommunityPick;
use App\Filament\Resources\CommunityPicks\Pages\EditCommunityPick;
use App\Filament\Resources\CommunityPicks\Pages\ListCommunityPicks;
use App\Filament\Resources\CommunityPicks\Pages\ViewCommunityPick;
use App\Filament\Resources\CommunityPicks\Schemas\CommunityPickForm;
use App\Filament\Resources\CommunityPicks\Schemas\CommunityPickInfolist;
use App\Filament\Resources\CommunityPicks\Tables\CommunityPicksTable;
use App\Models\CommunityPick;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommunityPickResource extends Resource
{
    protected static ?string $model = CommunityPick::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CommunityPickForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommunityPickInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunityPicksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommunityPicks::route('/'),
            'create' => CreateCommunityPick::route('/create'),
            'view' => ViewCommunityPick::route('/{record}'),
            'edit' => EditCommunityPick::route('/{record}/edit'),
        ];
    }
}
