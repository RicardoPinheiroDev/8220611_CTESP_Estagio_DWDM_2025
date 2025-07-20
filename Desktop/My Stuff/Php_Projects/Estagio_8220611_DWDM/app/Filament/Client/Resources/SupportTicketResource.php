<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\SupportTicketResource\Pages;
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
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $modelLabel = 'Support Ticket';
    protected static ?string $navigationLabel = 'Make a Ticket';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ticket Information')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),
                        Select::make('priority')
                            ->label('Priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('medium')
                            ->required(),
                    ])
                    ->columns(1),
                
                Section::make('Service Association')
                    ->schema([
                        Select::make('service_type')
                            ->label('Service Type')
                            ->options([
                                'general' => 'General Support',
                                'domain' => 'Domain',
                                'hosting' => 'Hosting',
                            ])
                            ->default('general')
                            ->live()
                            ->required(),
                        Select::make('service_id')
                            ->label('Related Service')
                            ->options(function ($get) {
                                $serviceType = $get('service_type');
                                $clientId = auth()->id();
                                
                                if (!$clientId || !$serviceType || $serviceType === 'general') {
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
            ->modifyQueryUsing(fn (Builder $query) => $query->where('client_id', auth()->id()))
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Ticket ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(50)
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
                    }),
                TextColumn::make('service_name')
                    ->label('Related To')
                    ->limit(30),
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
            ])
            ->actions([
                DeleteAction::make()
                    ->label('Remove')
                    ->visible(fn (SupportTicket $record) => in_array($record->getAttribute('status'), ['resolved', 'closed'])),
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'view' => Pages\ViewSupportTicket::route('/{record}'),
        ];
    }
}