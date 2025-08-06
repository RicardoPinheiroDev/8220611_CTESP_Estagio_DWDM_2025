<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrarsResource\Pages;
use App\Models\Registrar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RegistrarsResource extends Resource
{
    protected static ?string $model = Registrar::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Services';

    protected static ?int $navigationSort = 20;

    protected static ?string $modelLabel = 'Registrar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('website')
                ->url()
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('api_endpoint')
                ->label('API Endpoint URL')
                ->url()
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('api_key')
                ->label('API Key / Token')
                ->password()
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),


            Forms\Components\Select::make('status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->default('active')
                ->required(),

            Forms\Components\Textarea::make('notes')
                ->maxLength(65535)
                ->columnSpanFull()
                ->rows(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->url(fn (Model $record) => $record->website ?: '')
                    ->openUrlInNewTab()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('api_endpoint')
                    ->label('API Endpoint')
                    ->url(fn (Model $record) => $record->api_endpoint ?: '')
                    ->openUrlInNewTab()
                    ->color('secondary')
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRegistrars::route('/'),
            'create' => Pages\CreateRegistrars::route('/create'),
            'view' => Pages\ViewRegistrars::route('/{record}'),
            'edit' => Pages\EditRegistrars::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'website', 'api_endpoint'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Website' => $record->website,
            'API Endpoint' => $record->api_endpoint,
        ];
    }
}
