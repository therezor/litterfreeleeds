<?php

namespace App\Filament\Resources\Onboardings\Tables;

use App\Models\Postcode;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OnboardingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // The bag holder column below would otherwise be a query per row
            // for organisers and admins.
            ->modifyQueryUsing(fn (Builder $query) => $query->with('assignedBagHolder:id,name'))
            // Longest-waiting first — the order a bag holder should work
            // through them in.
            ->defaultSort('created_at', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->searchable(),
                TextColumn::make('postcode')
                    ->formatStateUsing(fn (?string $state): string => Postcode::format((string) $state))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Signed up')
                    ->since()
                    ->sortable(),
                // Three states, not two. A volunteer who has not confirmed their
                // email is not yet waiting on the bag holder — no email has
                // been sent about them — so chasing them would be premature.
                TextColumn::make('onboarded_at')
                    ->label('Status')
                    ->badge()
                    ->state(fn (User $record): string => match (true) {
                        $record->hasBeenOnboarded() => 'Onboarded',
                        $record->email_verified_at === null => 'Not confirmed',
                        default => 'Awaiting contact',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Onboarded' => 'success',
                        'Not confirmed' => 'gray',
                        default => 'warning',
                    })
                    ->tooltip(fn (User $record): ?string => $record->email_verified_at === null
                        ? 'They have not confirmed their email address yet, so we have not told you about them by email.'
                        : null)
                    ->sortable(),
                TextColumn::make('assignedBagHolder.name')
                    ->label('Purple Bag Holder')
                    ->placeholder('Not matched')
                    // Every row says the same name when a holder views their own
                    // list, so it only earns its place for organisers and admins.
                    ->visible(fn (): bool => static::viewerSeesEveryone()),
            ])
            ->filters([
                TernaryFilter::make('onboarded_at')
                    ->label('Onboarding')
                    ->nullable()
                    ->trueLabel('Onboarded')
                    ->falseLabel('Awaiting contact')
                    // The list is a to-do, so it opens on what is outstanding.
                    ->default(false),
            ])
            ->recordActions([
                static::onboardAction(),
                ViewAction::make(),
            ])
            ->toolbarActions([])
            ->emptyStateHeading('Nobody is waiting')
            ->emptyStateDescription('New volunteers matched to you will appear here as soon as they confirm their email address.')
            ->emptyStateIcon(Heroicon::OutlinedCheckBadge);
    }

    /**
     * The bag holder's half of onboarding: they contact the volunteer, hand over
     * purple bags, then confirm it here.
     *
     * ->authorize routes through UserPolicy::onboard(), which also hides it for
     * anyone already onboarded and for volunteers outside the viewer's scope.
     */
    public static function onboardAction(): Action
    {
        return Action::make('onboard')
            ->label('Mark as onboarded')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Mark this volunteer as onboarded')
            ->modalDescription('Confirm you have made contact and handed over purple bags.')
            ->modalSubmitActionLabel('Yes, they are set up')
            ->authorize('onboard')
            ->action(function (User $record): void {
                // forceFill: onboarded_at is deliberately not mass-assignable.
                $record->forceFill(['onboarded_at' => now()])->save();

                Notification::make()
                    ->success()
                    ->title($record->name.' is now onboarded')
                    ->send();
            });
    }

    protected static function viewerSeesEveryone(): bool
    {
        $viewer = Filament::auth()->user();

        return $viewer instanceof User && $viewer->seesAllVolunteers();
    }
}
