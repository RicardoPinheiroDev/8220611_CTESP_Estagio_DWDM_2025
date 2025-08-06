<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostingResource\Pages;
use App\Models\Hosting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema;

class HostingResource extends Resource
{
    protected static ?string $model = Hosting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cloud';
    protected static ?string $navigationGroup = 'Home';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('client_id')
                ->relationship('client', 'name')
                ->searchable()
                ->required(),

            Forms\Components\Select::make('domain_id')
                ->relationship('domain', 'name')
                ->searchable()
                ->label('Domain (optional)')
                ->nullable(),

            Forms\Components\TextInput::make('plan_name')
                ->label('Plan Name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('storage_limit')
                ->label('Storage Limit')
                ->required()
                ->maxLength(50),

            Forms\Components\Select::make('server_id')
                ->relationship('server', 'name', function ($query) {
                    if (!Schema::hasTable('servers')) {
                        return $query->whereRaw('1 = 0');
                    }
                    return $query;
                })
                ->searchable()
                ->nullable(),

            Forms\Components\TextInput::make('plan_price')
                ->label('Plan Price')
                ->prefix('€')
                ->numeric()
                ->required()
                ->step(0.01),

            Forms\Components\TextInput::make('next_renewal_price')
                ->label('Next Renewal Price')
                ->prefix('€')
                ->numeric()
                ->step(0.01)
                ->nullable(),

            Forms\Components\Select::make('payment_status')
                ->label('Payment Status')
                ->options([
                    'paid' => 'Paid',
                    'unpaid' => 'Unpaid',
                    'pending' => 'Pending',
                ])
                ->required(),

            Forms\Components\DateTimePicker::make('starts_at')
                ->label('Start Date')
                ->required(),

            Forms\Components\DateTimePicker::make('expires_at')
                ->label('Expiration Date')
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'active' => 'Active',
                    'suspended' => 'Suspended',
                    'cancelled' => 'Cancelled',
                ])
                ->required(),

            Forms\Components\Textarea::make('plan_features')
                ->label('Plan Features')
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->sortable(),

                Tables\Columns\TextColumn::make('domain.name')
                    ->label('Domain')
                    ->searchable(),

                Tables\Columns\TextColumn::make('plan_name')
                    ->label('Plan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('storage_limit')
                    ->label('Storage'),

                Tables\Columns\TextColumn::make('plan_price')
                    ->label('Price')
                    ->money('EUR'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostings::route('/'),
            'create' => Pages\CreateHosting::route('/create'),
            'edit' => Pages\EditHosting::route('/{record}/edit'),
        ];
    }
}
