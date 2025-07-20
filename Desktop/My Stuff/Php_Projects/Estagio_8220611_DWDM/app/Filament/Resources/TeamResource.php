<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use App\Models\User;
use App\Models\Department;
use App\Enums\UserType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\Facades\Hash;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $modelLabel = 'Team Member';
    protected static ?string $navigationLabel = 'Team Members';
    protected static ?string $navigationGroup = 'Organization';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('New Team Member Information')
                    ->description('Create a new user account for this team member')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required(fn ($get) => !$get('promote_user_id'))
                            ->maxLength(255)
                            ->disabled(fn ($get) => $get('promote_user_id')),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(fn ($get) => !$get('promote_user_id'))
                            ->maxLength(255)
                            ->unique(Team::class, 'email', ignoreRecord: true)
                            ->disabled(fn ($get) => $get('promote_user_id')),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn ($get) => !$get('promote_user_id'))
                            ->maxLength(255)
                            ->minLength(8)
                            ->disabled(fn ($get) => $get('promote_user_id')),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                UserType::EMPLOYEE->value => 'Employee',
                                UserType::ADMIN->value => 'Admin',
                            ])
                            ->default(UserType::EMPLOYEE->value)
                            ->required(fn ($get) => !$get('promote_user_id'))
                            ->disabled(fn ($get) => $get('promote_user_id')),
                        Toggle::make('status')
                            ->label('Active')
                            ->default(true)
                            ->required(fn ($get) => !$get('promote_user_id'))
                            ->disabled(fn ($get) => $get('promote_user_id')),
                    ])
                    ->columns(2),
                Section::make('Department Assignment')
                    ->description('Assign the team member to a department')
                    ->schema([
                        Select::make('department_id')
                            ->label('Select Department')
                            ->options(Department::where('status', true)->pluck('department', 'id'))
                            ->searchable()
                            ->required()
                            ->placeholder('Select a department'),
                    ])
                    ->columns(1),
                Section::make('Promote Existing User (Optional)')
                    ->description('Promote an existing user to team member instead of creating a new one')
                    ->schema([
                        Select::make('promote_user_id')
                            ->label('Search User to Promote')
                            ->options(User::whereIn('type', [UserType::ADMIN, UserType::EMPLOYEE])
                                ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->placeholder('Search for existing user to promote')
                            ->helperText('This will copy user data to create a team member')
                            ->reactive(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Team Member')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (UserType $state): string => match($state) {
                        UserType::ADMIN => 'Admin',
                        UserType::EMPLOYEE => 'Employee',
                        default => 'Unknown',
                    })
                    ->color(fn (UserType $state): string => match ($state) {
                        UserType::ADMIN => 'danger',
                        UserType::EMPLOYEE => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('department.department')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('status')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->relationship('department', 'department')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        UserType::ADMIN->value => 'Admin',
                        UserType::EMPLOYEE->value => 'Employee',
                    ]),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->queries(
                        true: fn ($query) => $query->where('status', true),
                        false: fn ($query) => $query->where('status', false),
                    ),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}