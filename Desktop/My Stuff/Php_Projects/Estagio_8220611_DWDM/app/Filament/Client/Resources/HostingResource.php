<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\HostingResource\Pages;
use App\Models\Hosting;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HostingResource extends Resource
{
    protected static ?string $model = Hosting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationGroup = 'Home';

    protected static ?string $navigationLabel = 'Hosting';

    protected static ?string $pluralLabel = 'Current Hostings';
    
    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('domain.name')
                    ->label('Domain')
                    ->searchable()
                    ->placeholder('No domain assigned'),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Hosting Plan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Started')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at->isPast() ? 'danger' : ($record->expires_at->diffInDays() < 30 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('next_renewal_price')
                    ->label('Renewal Price')
                    ->money('EUR')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('More Info'),
            ])
            ->defaultSort('expires_at', 'asc')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('client_id', auth()->id()))
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostings::route('/'),
            'view' => Pages\ViewHosting::route('/{record}'),
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