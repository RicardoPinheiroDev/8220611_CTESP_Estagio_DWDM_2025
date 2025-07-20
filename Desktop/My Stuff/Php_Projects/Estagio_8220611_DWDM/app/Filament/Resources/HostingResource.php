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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('account_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('domain_id')
                    ->relationship('domain', 'name')
                    ->searchable()
                    ->label('Domain (optional)')
                    ->nullable(),
                Forms\Components\Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('server_id')
                    ->relationship('server', 'name', function ($query) {
                        if (!Schema::hasTable('servers')) {
                            return $query->whereRaw('1 = 0');
                        }
                        return $query;
                    })
                    ->searchable()
                    ->nullable(),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->required(),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'pending' => 'Pending',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('next_renewal_price')
                    ->numeric()
                    ->prefix('€'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('domain.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expires_at')
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
