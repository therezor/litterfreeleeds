<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Postcode;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Both the roles badge and the onboarding badge read roles —
            // without this that is two extra queries per row.
            ->modifyQueryUsing(fn (Builder $query) => $query->with('roles'))
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('postcode')
                    ->formatStateUsing(fn (?string $state): string => Postcode::format((string) $state))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->searchable(),
                // Onboarding is a volunteer's journey, so the badge is blank for
                // staff — an admin reading "Awaiting contact" against their own
                // row looks like something is outstanding when nothing is.
                TextColumn::make('onboarded_at')
                    ->label('Onboarding')
                    ->badge()
                    ->state(fn (User $record): ?string => $record->hasRole(User::ROLE_PICKER)
                        ? ($record->hasBeenOnboarded() ? 'Onboarded' : 'Awaiting contact')
                        : null)
                    ->color(fn (?string $state): string => $state === 'Onboarded' ? 'success' : 'warning')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('assignedBagHolder.name')
                    ->label('Purple Bag Holder')
                    ->placeholder('Not matched')
                    // Redundant noise for a bag holder looking at their own list.
                    ->visible(fn (): bool => static::viewerSeesEveryone()),
                TextColumn::make('created_at')
                    ->label('Signed up')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('onboarded_at')
                    ->label('Onboarding')
                    ->nullable()
                    ->trueLabel('Onboarded')
                    ->falseLabel('Awaiting contact'),
            ])
            // No onboard action here — that is the Onboarding resource's job.
            // This list is account administration; onboarding is a workflow.
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                static::deleteAction(),
            ])
            // No bulk delete: community_picks.responsible_user_id restricts on
            // delete, so a bulk run that hits one owner fails the whole batch
            // with a database error rather than a usable message.
            ->toolbarActions([]);
    }

    /**
     * A pick must always have someone responsible for it, so the database
     * refuses to delete a user who still owns one. Catch that here and say so.
     *
     * Volunteers assigned to a bag holder are not a blocker — that column is
     * nullOnDelete, so they simply become unmatched. They are warned about
     * instead, since silently unmatching people is worth knowing.
     */
    public static function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->before(function (User $record, DeleteAction $action): void {
                if ($record->communityPicks()->exists()) {
                    Notification::make()
                        ->danger()
                        ->title('This volunteer is responsible for picks')
                        ->body('Reassign their picks to someone else before deleting the account.')
                        ->send();

                    $action->cancel();

                    return;
                }

                $pickerCount = $record->assignedPickers()->count();

                if ($pickerCount > 0) {
                    Notification::make()
                        ->warning()
                        ->title($pickerCount.' '.str('volunteer')->plural($pickerCount).' left unmatched')
                        ->body('They were assigned to this bag holder. Reassign them from each volunteer\'s record.')
                        ->send();
                }
            });
    }

    protected static function viewerSeesEveryone(): bool
    {
        $viewer = Filament::auth()->user();

        return $viewer instanceof User && $viewer->seesAllVolunteers();
    }
}
