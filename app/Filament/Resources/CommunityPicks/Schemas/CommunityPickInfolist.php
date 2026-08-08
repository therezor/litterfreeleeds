<?php

namespace App\Filament\Resources\CommunityPicks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommunityPickInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('The pick')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('slug')
                            ->label('Public URL')
                            ->url(fn ($record): string => url('/upcoming-picks/'.$record->slug))
                            ->openUrlInNewTab(),
                        TextEntry::make('excerpt')
                            ->label('Short summary')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->label('Full description')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('When')
                    ->schema([
                        TextEntry::make('date')->date('l j F Y'),
                        TextEntry::make('time_range')->label('Time'),
                    ])
                    ->columns(2),

                Section::make('Where')
                    ->schema([
                        TextEntry::make('location')->label('Meeting point'),
                        TextEntry::make('formatted_postcode')
                            ->label('Postcode')
                            ->url(fn ($record): string => $record->directions_url)
                            ->openUrlInNewTab(),
                        TextEntry::make('ward.name')->label('Area')->badge()->placeholder('Not imported yet'),
                        TextEntry::make('district.name')->label('City')->placeholder('Not imported yet'),
                        TextEntry::make('responsibleUser.name')->label('Responsible person'),
                    ])
                    ->columns(2),

                Section::make('Record')
                    ->schema([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
