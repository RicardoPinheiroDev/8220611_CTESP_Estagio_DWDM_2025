<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostingPlanResource\Pages;
use App\Models\HostingPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class HostingPlanResource extends Resource
{
    protected static ?string $model = HostingPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationGroup = 'Services';
    
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('storage_limit')
                    ->label('Storage Limit')
                    ->required()
                    ->helperText('Example: 10 GB, 500 MB, 1 TB')
                    ->maxLength(50),
                    
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('€')
                    ->step(0.01),
                    
                Forms\Components\Textarea::make('features')
                    ->label('Features (one per line)')
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
                    
                Tables\Columns\TextColumn::make('storage_limit')
                    ->label('Storage')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),
                    
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
            ])
            ->defaultSort('price', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostingPlans::route('/'),
            'create' => Pages\CreateHostingPlan::route('/create'),
            'view' => Pages\ViewHostingPlan::route('/{record}'),
            'edit' => Pages\EditHostingPlan::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'storage_limit'];
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var \App\Models\HostingPlan $record */
        return [
            'Storage' => $record->storage_limit,
            'Price' => '€' . number_format($record->price, 2),
        ];
    }
}
