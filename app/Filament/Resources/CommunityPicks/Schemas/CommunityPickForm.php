<?php

namespace App\Filament\Resources\CommunityPicks\Schemas;

use App\Models\District;
use App\Models\Postcode;
use App\Models\User;
use App\Models\Ward;
use App\Rules\KnownUkPostcode;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CommunityPickForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('The pick')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('excerpt')
                            ->label('Short summary')
                            ->helperText('One line. Shown in the listing and used as the page description.')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Full description')
                            ->helperText("Optional. Shown only on the pick's own page.")
                            ->rows(6)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('When')
                    ->schema([
                        DatePicker::make('date')
                            ->required()
                            ->native(false)
                            ->displayFormat('D j M Y')
                            ->closeOnDateSelection(),

                        // Keep these native. ->native(false) makes the picker
                        // dehydrate a full 'Y-m-d H:i:s' into a time column.
                        TimePicker::make('time_from')
                            ->label('Start time')
                            ->required()
                            ->seconds(false),

                        TimePicker::make('time_to')
                            ->label('End time')
                            ->required()
                            ->seconds(false)
                            ->after('time_from'),
                    ])
                    ->columns(3),

                Section::make('Where')
                    ->schema([
                        TextInput::make('location')
                            ->label('Meeting point')
                            ->helperText('Free text, e.g. "The car park by the Lakeside Cafe".')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('postcode')
                            ->required()
                            ->maxLength(8)
                            ->rules([new KnownUkPostcode])
                            ->live(onBlur: true)
                            ->helperText('Must exist in the imported postcode directory. Coordinates and area are filled in from it automatically.'),

                        // Derived, not chosen. Shown so a coordinator can sanity
                        // check the postcode they just typed.
                        TextEntry::make('place')
                            ->label('Area')
                            ->state(function (Get $get): string {
                                $postcode = Postcode::find(Postcode::normalise($get('postcode')));

                                if ($postcode === null) {
                                    return '—';
                                }

                                return collect([
                                    Ward::query()->whereKey($postcode->ward_code)->value('name'),
                                    District::query()->whereKey($postcode->district_code)->value('name'),
                                ])->filter()->implode(', ') ?: '—';
                            })
                            ->helperText('Worked out from the postcode. Empty until an ONSPD release has been imported.'),

                        // Scoped to users who hold a role OTHER than Picker.
                        // "Holds any role" used to be enough, because registration
                        // left volunteers roleless — but every public registrant is
                        // now a Picker, so that test would offer every stranger who
                        // has ever signed up.
                        Select::make('responsible_user_id')
                            ->label('Responsible person')
                            ->relationship(
                                'responsibleUser',
                                'name',
                                fn (Builder $query) => $query->whereHas(
                                    'roles',
                                    fn (Builder $roles) => $roles->whereNot('name', User::ROLE_PICKER),
                                ),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Bag holders, organisers and admins can be made responsible for a pick.'),
                    ])
                    ->columns(2),

                Section::make('Advanced')
                    ->schema([
                        TextInput::make('slug')
                            ->helperText('The public URL. Generated from the name and date — changing it breaks existing links.')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ])
                    ->collapsed()
                    ->collapsible()
                    ->visibleOn('edit'),
            ]);
    }
}
