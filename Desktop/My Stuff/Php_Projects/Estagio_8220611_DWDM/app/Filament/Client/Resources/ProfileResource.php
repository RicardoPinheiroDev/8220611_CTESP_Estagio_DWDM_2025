<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ProfileResource\Pages;
use App\Filament\Client\Resources\ProfileResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProfileResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Home';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $modelLabel = 'Profile';

    protected static ?int $navigationSort = -10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Billing Information')
                    ->schema([
                        Forms\Components\TextInput::make('billing_name')
                            ->label('Billing Name')
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('billing_address')
                            ->label('Billing Address')
                            ->rows(3)
                            ->maxLength(500),
                        
                        Forms\Components\TextInput::make('vat_number')
                            ->label('VAT Number')
                            ->maxLength(50),
                    ])
                    ->columns(1),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Service Overview')
                    ->schema([
                        TextEntry::make('active_domains')
                            ->label('Active Domains')
                            ->state(function (Client $record): string {
                                return (string) $record->domains()->count();
                            })
                            ->badge()
                            ->color('success'),
                        
                        TextEntry::make('active_hostings')
                            ->label('Active Hostings')
                            ->state(function (Client $record): string {
                                return (string) $record->hostings()->count();
                            })
                            ->badge()
                            ->color('primary'),
                        
                    ])
                    ->columns(2),

                Section::make('Personal Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Full Name'),
                        
                        TextEntry::make('email')
                            ->label('Email Address'),
                        
                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->placeholder('Not provided'),
                    ])
                    ->columns(2),

                Section::make('Billing Information')
                    ->schema([
                        TextEntry::make('billing_name')
                            ->label('Billing Name')
                            ->placeholder('Not provided'),
                        
                        TextEntry::make('billing_address')
                            ->label('Billing Address')
                            ->placeholder('Not provided'),
                        
                        TextEntry::make('vat_number')
                            ->label('VAT Number')
                            ->placeholder('Not provided'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('Not provided'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $client = Auth::guard('client')->user();
                return $query->where('id', $client->getAuthIdentifier());
            })
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ViewProfile::route('/'),
            'edit' => Pages\EditProfile::route('/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $client = Auth::guard('client')->user();
        return parent::getEloquentQuery()->where('id', $client->getAuthIdentifier());
    }
}