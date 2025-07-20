<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServerResource\Pages;
use App\Models\Server;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;

    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'Services';
    protected static ?int $navigationSort = 10;
    
    protected static ?string $modelLabel = 'Server';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('ip_address')
                    ->label('IP Address')
                    ->required()
                    ->ipv4()
                    ->maxLength(45),
                    
                Forms\Components\TextInput::make('provider')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
                    
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(function (Server $record) {
                        try {
                            return $record->provider;
                        } catch (\Exception $e) {
                            return '';
                        }
                    })
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('IP address copied!')
                    ->copyMessageDuration(1500),
                    
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->icon(fn () => 'heroicon-o-signal')
                    ->color('success')
                    ->tooltip('Click view and then Ping Server to check status'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->options(function () {
                        if (!Schema::hasTable('servers')) {
                            return [];
                        }
                        try {
                            return Server::query()->pluck('provider', 'provider')->unique();
                        } catch (\Exception $e) {
                            return [];
                        }
                    }),
                    
                Tables\Filters\SelectFilter::make('location')
                    ->options(function () {
                        if (!Schema::hasTable('servers')) {
                            return [];
                        }
                        try {
                            return Server::query()->pluck('location', 'location')->unique();
                        } catch (\Exception $e) {
                            return [];
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'view' => Pages\ViewServer::route('/{record}'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'ip_address', 'provider', 'location'];
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (!($record instanceof Server)) {
            return [];
        }
        
        return [
            'IP' => $record->ip_address,
            'Provider' => $record->provider,
        ];
    }
}
