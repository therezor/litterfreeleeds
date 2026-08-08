<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
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
     */
    public static function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->before(function (User $record, DeleteAction $action): void {
                if (! $record->communityPicks()->exists()) {
                    return;
                }

                Notification::make()
                    ->danger()
                    ->title('This volunteer is responsible for picks')
                    ->body('Reassign their picks to someone else before deleting the account.')
                    ->send();

                $action->cancel();
            });
    }
}
