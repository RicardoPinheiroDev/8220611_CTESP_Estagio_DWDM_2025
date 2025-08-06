<?php

namespace App\Filament\Resources;

use App\Enums\UserType;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    
    protected static ?string $modelLabel = 'User';
    protected static ?string $navigationLabel = 'User List';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Organization';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'This email address is already registered. Please use a different email.',
                    ]),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->maxLength(255),
                Forms\Components\Toggle::make('status')
                    ->label('Active')
                    ->default(true),
                Forms\Components\Select::make('type')
                    ->options(UserType::class)
                    ->required()
                    ->default(UserType::CLIENT),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('status')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        if ($state instanceof UserType) {
                            return match($state) {
                                UserType::CLIENT => 'Client',
                                UserType::ADMIN => 'Admin', 
                                UserType::EMPLOYEE => 'Employee',
                            };
                        }
                        return match($state) {
                            'client' => 'Client',
                            'admin' => 'Admin',
                            'employee' => 'Employee',
                            default => ucfirst($state),
                        };
                    })
                    ->color(function ($state): string {
                        if ($state instanceof UserType) {
                            return match ($state) {
                                UserType::CLIENT => 'success',
                                UserType::ADMIN => 'danger',
                                UserType::EMPLOYEE => 'info',
                            };
                        }
                        return match ($state) {
                            'client' => 'success',
                            'admin' => 'danger',
                            'employee' => 'info',
                            default => 'gray',
                        };
                    }),
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
                SelectFilter::make('type')
                    ->options(UserType::class),
                Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('status', true))
                    ->label('Only active users'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}