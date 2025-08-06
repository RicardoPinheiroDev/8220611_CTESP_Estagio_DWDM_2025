<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialMovementResource\Pages;
use App\Models\FinancialMovement;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FinancialMovementResource extends Resource
{
    protected static ?string $model = FinancialMovement::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $modelLamasbel = 'Financial Movement';
    protected static ?string $navigationLabel = 'Financial Movements';
    protected static ?string $navigationGroup = 'Financial';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Movement Information')
                    ->schema([
                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('invoice_id')
                            ->label('Related Invoice (Optional)')
                            ->relationship('invoice', 'invoice_number')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Select::make('type')
                            ->label('Movement Type')
                            ->options([
                                'payment' => 'Payment',
                                'adjustment' => 'Adjustment',
                                'credit' => 'Credit',
                                'refund' => 'Refund',
                            ])
                            ->required()
                            ->default('payment'),
                        TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Payment Details')
                    ->schema([
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'mbway' => 'MbWay',
                                'paypal' => 'Paypal',
                            ])
                            ->required()
                            ->live(),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required()
                            ->live(),
                        
                        DateTimePicker::make('paid_at')
                            ->label('Paid At')
                            ->visible(fn ($get) => $get('status') === 'paid'),
                        
                        // MBWay Fields
                        TextInput::make('mbway_phone')
                            ->label('MbWay Phone Number')
                            ->placeholder('e.g., +351 912 345 678')
                            ->maxLength(255)
                            ->visible(fn ($get) => $get('payment_method') === 'mbway'),
                        
                        // PayPal Fields
                        TextInput::make('paypal_email')
                            ->label('PayPal Email')
                            ->placeholder('e.g., user@example.com')
                            ->email()
                            ->maxLength(255)
                            ->visible(fn ($get) => $get('payment_method') === 'paypal'),
                        TextInput::make('paypal_transaction_id')
                            ->label('PayPal Transaction ID')
                            ->placeholder('e.g., 8RS12345A6789012B')
                            ->maxLength(255)
                            ->visible(fn ($get) => $get('payment_method') === 'paypal'),
                        
                        DateTimePicker::make('processed_at')
                            ->label('Processed At')
                            ->default(now())
                            ->required(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'payment',
                        'info' => 'credit',
                        'warning' => 'adjustment',
                        'danger' => 'refund',
                    ]),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger'),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->placeholder('—'),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? 'pending')),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'payment' => 'Payment',
                        'adjustment' => 'Adjustment',
                        'credit' => 'Credit',
                        'refund' => 'Refund',
                    ]),
                Tables\Filters\SelectFilter::make('client_id')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('amount_range')
                    ->form([
                        TextInput::make('amount_from')
                            ->numeric()
                            ->placeholder('From'),
                        TextInput::make('amount_to')
                            ->numeric()
                            ->placeholder('To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['amount_from'], fn ($query, $amount) => $query->where('amount', '>=', $amount))
                            ->when($data['amount_to'], fn ($query, $amount) => $query->where('amount', '<=', $amount));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_as_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Payment as Paid')
                    ->modalDescription('Are you sure you want to mark this financial movement as paid?')
                    ->action(function (FinancialMovement $record) {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Payment Marked as Paid')
                            ->body('The financial movement has been marked as paid.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (FinancialMovement $record) => 
                        $record->type === 'payment' && 
                        ($record->status ?? 'pending') === 'pending'
                    ),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('processed_at', 'desc')
            ->filtersFormColumns(0)
            ->headerActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinancialMovements::route('/'),
            'create' => Pages\CreateFinancialMovement::route('/create'),
            'edit' => Pages\EditFinancialMovement::route('/{record}/edit'),
        ];
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        $data['created_by'] = Auth::id();
        return $data;
    }
}