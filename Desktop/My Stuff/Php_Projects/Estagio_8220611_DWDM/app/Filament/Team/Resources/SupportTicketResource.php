<?php

namespace App\Filament\Team\Resources;

use App\Filament\Team\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;

use App\Models\Domain;
use App\Models\Hosting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;


class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $modelLabel = 'Support Ticket';
    protected static ?string $navigationLabel = 'Status';
    protected static ?string $navigationGroup = 'Support Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ticket Information')
                    ->schema([
                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull()
                            ->rows(4),
                        Select::make('priority')
                            ->label('Priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->required()
                            ->default('medium'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'open' => 'Open',
                                'in_progress' => 'In Progress',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->required()
                            ->default('open'),
                    ])
                    ->columns(2),
                Section::make('Service Information')
                    ->schema([
                        Select::make('service_type')
                            ->label('Service Type')
                            ->options([
                                'general' => 'General',
                                'domain' => 'Domain',
                                'hosting' => 'Hosting',
                            ])
                            ->required()
                            ->default('general')
                            ->reactive(),
                        Select::make('service_id')
                            ->label('Service')
                            ->options(function ($get) {
                                $serviceType = $get('service_type');
                                $clientId = $get('client_id');
                                
                                if (!$clientId || !$serviceType) {
                                    return [];
                                }
                                
                                return match ($serviceType) {
                                    'domain' => Domain::where('client_id', $clientId)->pluck('name', 'id'),
                                    'hosting' => Hosting::where('client_id', $clientId)->pluck('account_name', 'id'),
                                    default => [],
                                };
                            })
                            ->searchable()
                            ->visible(fn ($get) => $get('service_type') !== 'general'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Ticket ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(40)
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'open',
                        'warning' => 'in_progress',
                        'success' => 'resolved',
                        'gray' => 'closed',
                    ]),
                BadgeColumn::make('priority')
                    ->label('Priority')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => ['high', 'urgent'],
                    ]),
                TextColumn::make('service_type')
                    ->label('Service')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'general' => 'General',
                        'domain' => 'Domain',
                        'hosting' => 'Hosting',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'domain' => 'success',
                        'hosting' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                Tables\Filters\SelectFilter::make('service_type')
                    ->options([
                        'general' => 'General',
                        'domain' => 'Domain',
                        'hosting' => 'Hosting',
                    ]),
                Tables\Filters\SelectFilter::make('client_id')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Action::make('mark_resolved')
                    ->label('Mark as Resolved')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (SupportTicket $record) => $record->update(['status' => 'resolved']))
                    ->visible(fn (SupportTicket $record) => in_array($record->status, ['open', 'in_progress'])),
                DeleteAction::make()
                    ->label('Remove Ticket'),
                Tables\Actions\ViewAction::make()
                    ->label('Reply Client')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'view' => Pages\ViewSupportTicket::route('/{record}'),
        ];
    }
}