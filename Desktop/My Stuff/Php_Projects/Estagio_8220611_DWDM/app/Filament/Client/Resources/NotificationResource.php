<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\NotificationResource\Pages;
use App\Models\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification as FilamentNotification;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static ?string $navigationLabel = 'Notifications';
    
    protected static ?string $navigationGroup = 'Notifications';
    
    protected static ?int $navigationSort = 2;

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->sortable(false)
                    ->searchable(false),
                
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->sortable(false)
                    ->searchable(false),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(false)
                    ->searchable(false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('filter')
                    ->options([
                        'all' => 'All',
                        'new' => 'New',
                        'latest' => 'Latest',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'new') {
                            return $query->whereNull('read_at');
                        }
                        if ($data['value'] === 'latest') {
                            return $query->where('created_at', '>=', now()->subDays(7));
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation(false),
            ])
            ->headerActions([
                Tables\Actions\Action::make('delete_all')
                    ->label('Clear All')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Clear All Notifications')
                    ->modalDescription('Are you sure you want to clear all your notifications? This action cannot be undone.')
                    ->action(function () {
                        Notification::where('user_id', auth()->id())->delete();
                        
                        FilamentNotification::make()
                            ->title('Notifications cleared')
                            ->body('Notifications have been successfully removed.')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No notifications yet')
            ->emptyStateDescription('Notifications they will appear here.')
            ->emptyStateIcon('heroicon-o-bell')
            ->recordUrl(null)
            ->filtersTriggerAction(null)
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'view' => Pages\ViewNotification::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('client_id', auth()->id())->unread()->count();
    }
}
