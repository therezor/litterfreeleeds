<?php

namespace App\Filament\Resources\Onboardings\Schemas;

use App\Models\Postcode;
use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * What a bag holder needs in order to make contact — and nothing else. No
 * roles, no account internals.
 */
class OnboardingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Get in touch')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->label('Email address')
                            ->copyable()
                            ->copyMessage('Email address copied'),
                        TextEntry::make('postcode')
                            ->formatStateUsing(fn (?string $state): string => Postcode::format((string) $state))
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->label('Signed up')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Status')
                    ->schema([
                        TextEntry::make('onboarded_at')
                            ->label('Onboarding')
                            ->badge()
                            ->state(fn (User $record): string => $record->hasBeenOnboarded()
                                ? 'Onboarded '.$record->onboarded_at?->format('j M Y')
                                : 'Awaiting contact')
                            ->color(fn (User $record): string => $record->hasBeenOnboarded() ? 'success' : 'warning'),
                        TextEntry::make('email_verified_at')
                            ->label('Email confirmed')
                            ->dateTime()
                            ->placeholder('Not confirmed yet'),
                    ])
                    ->columns(2),
            ]);
    }
}
