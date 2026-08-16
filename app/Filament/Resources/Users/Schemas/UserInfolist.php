<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Postcode;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Volunteer')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->label('Email address')
                            ->copyable(),
                        TextEntry::make('postcode')
                            ->formatStateUsing(fn (?string $state): string => Postcode::format((string) $state))
                            ->placeholder('—'),
                        TextEntry::make('roles.name')
                            ->label('Roles')
                            ->badge()
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('Onboarding')
                    ->schema([
                        TextEntry::make('assignedBagHolder.name')
                            ->label('Purple Bag Holder')
                            ->placeholder('Not matched yet'),
                        TextEntry::make('onboarded_at')
                            ->label('Onboarded')
                            ->dateTime()
                            ->badge()
                            ->color(fn (?string $state): string => $state === null ? 'warning' : 'success')
                            ->placeholder('Awaiting contact'),
                        TextEntry::make('email_verified_at')
                            ->label('Email confirmed')
                            ->dateTime()
                            ->placeholder('Not confirmed'),
                        TextEntry::make('terms_accepted_at')
                            ->label('Agreed to data processing')
                            ->dateTime()
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('Record')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Signed up')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('—'),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
