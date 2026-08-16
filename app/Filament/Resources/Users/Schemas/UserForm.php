<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use App\Rules\KnownUkPostcode;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        DateTimePicker::make('email_verified_at'),
                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                    ])
                    ->columns(2),

                Section::make('Volunteering')
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            // Drives the postcode requirement below.
                            ->live(),

                        TextInput::make('postcode')
                            ->maxLength(8)
                            ->rules([new KnownUkPostcode])
                            ->live(onBlur: true)
                            // A bag holder with no postcode cannot be matched to
                            // anyone — the nearest-holder query filters them out —
                            // so they would silently never receive a volunteer.
                            ->required(fn (Get $get): bool => static::rolesInclude($get('roles'), User::ROLE_BAG_HOLDER))
                            ->helperText('Required for Purple Bag Holders — it is what new volunteers are matched against. Coordinates are filled in from it automatically.'),

                        Select::make('assigned_bag_holder_id')
                            ->label('Purple Bag Holder')
                            ->relationship(
                                'assignedBagHolder',
                                'name',
                                fn (Builder $query) => $query->whereHas(
                                    'roles',
                                    fn (Builder $roles) => $roles->where('name', User::ROLE_BAG_HOLDER),
                                ),
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Set automatically at registration to the nearest holder. Change it if someone should be looked after by a different one.'),

                        DateTimePicker::make('onboarded_at')
                            ->label('Onboarded')
                            ->helperText('Set when a bag holder confirms they have made contact and handed over bags.'),

                        DateTimePicker::make('terms_accepted_at')
                            ->label('Agreed to data processing')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Recorded at registration. Read-only — it is a record of consent.'),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * The roles select holds role ids, so the name has to be looked up.
     */
    protected static function rolesInclude(mixed $roleIds, string $name): bool
    {
        if (! is_array($roleIds) || $roleIds === []) {
            return false;
        }

        return Role::query()
            ->whereKey($roleIds)
            ->where('name', $name)
            ->exists();
    }
}
