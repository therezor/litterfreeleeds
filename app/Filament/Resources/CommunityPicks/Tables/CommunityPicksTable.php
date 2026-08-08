<?php

namespace App\Filament\Resources\CommunityPicks\Tables;

use App\Models\CommunityPick;
use App\Models\Postcode;
use App\Models\Ward;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommunityPicksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight(FontWeight::Medium)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->date('D j M Y')
                    ->sortable(),

                // Not ->time(): that parses the naive wall-clock string through
                // Carbon and applies the panel timezone to it.
                TextColumn::make('time_range')
                    ->label('Time'),
                TextColumn::make('location')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('postcode')
                    ->formatStateUsing(fn (string $state): string => Postcode::format($state))
                    ->searchable(),
                TextColumn::make('place_label')
                    ->label('Area')
                    ->badge()
                    ->placeholder('Not imported yet'),
                TextColumn::make('district.name')
                    ->label('City')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('responsibleUser.name')
                    ->label('Responsible')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date')
            ->filters([
                TernaryFilter::make('timeframe')
                    ->label('Timeframe')
                    ->placeholder('All picks')
                    ->trueLabel('Upcoming only')
                    ->falseLabel('Past only')
                    ->queries(
                        true: fn (Builder $query) => $query->where('date', '>=', CommunityPick::todayInLeeds()),
                        false: fn (Builder $query) => $query->where('date', '<', CommunityPick::todayInLeeds()),
                    )
                    ->default(true),

                // Areas are electoral wards under the hood. Only offers ones
                // that actually have picks — the wards table holds every ward in
                // the UK once ONSPD has been imported.
                SelectFilter::make('ward')
                    ->label('Area')
                    ->options(fn (): array => Ward::query()
                        ->whereIn('code', Postcode::query()
                            ->whereIn('postcode', CommunityPick::query()->select('postcode'))
                            ->select('ward_code'))
                        ->orderBy('name')
                        ->pluck('name', 'code')
                        ->all())
                    ->searchable()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['values'] ?? [],
                        fn (Builder $query, array $wardCodes) => $query->whereIn(
                            'postcode',
                            Postcode::query()->whereIn('ward_code', $wardCodes)->select('postcode')
                        )
                    )),

                SelectFilter::make('responsibleUser')
                    ->label('Responsible person')
                    ->relationship('responsibleUser', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
