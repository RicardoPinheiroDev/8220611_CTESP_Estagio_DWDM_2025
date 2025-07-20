<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\FinancialMovementResource\Pages;
use App\Models\FinancialMovement;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class FinancialMovementResource extends Resource
{
    protected static ?string $model = FinancialMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Financial Movements';

    protected static ?string $pluralLabel = 'Financial History';
    
    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'payment',
                        'info' => 'credit',
                        'warning' => 'adjustment',
                        'danger' => 'refund',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'payment' => 'Payment',
                        'credit' => 'Credit',
                        'adjustment' => 'Adjustment',
                        'refund' => 'Refund',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Processed At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'payment' => 'Payments',
                        'adjustment' => 'Adjustments',
                        'credit' => 'Credits',
                        'refund' => 'Refunds',
                    ]),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($query, $date) => $query->whereDate('processed_at', '>=', $date))
                            ->when($data['until'], fn ($query, $date) => $query->whereDate('processed_at', '<=', $date));
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->label('View Details'),
            ])
            ->defaultSort('processed_at', 'desc')
            ->toggleColumnsTriggerAction(fn () => null)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('client_id', auth()->id()))
            ->heading('Your Financial Movements')
            ->description('View all financial transactions and account movements')
            ->emptyStateHeading('No Financial Movements')
            ->emptyStateDescription('No financial movements have been recorded for your account yet.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinancialMovements::route('/'),
            'view' => Pages\ViewFinancialMovement::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}