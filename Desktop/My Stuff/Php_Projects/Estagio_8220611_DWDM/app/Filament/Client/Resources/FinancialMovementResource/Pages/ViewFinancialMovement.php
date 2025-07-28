<?php

namespace App\Filament\Client\Resources\FinancialMovementResource\Pages;

use App\Filament\Client\Resources\FinancialMovementResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewFinancialMovement extends ViewRecord
{
    protected static string $resource = FinancialMovementResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Movement Details')
                    ->schema([
                        TextEntry::make('type')
                            ->label('Movement Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'payment' => 'success',
                                'credit' => 'info',
                                'adjustment' => 'warning',
                                'refund' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'payment' => 'Payment',
                                'credit' => 'Credit',
                                'adjustment' => 'Adjustment',
                                'refund' => 'Refund',
                                default => $state,
                            }),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('EUR')
                            ->color(fn ($state) => $state >= 0 ? 'success' : 'danger'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('processed_at')
                            ->label('Processing Date')
                            ->dateTime(),
                    ])
                    ->columns(2),
                    
                Section::make('Payment Information')
                    ->schema([
                        TextEntry::make('payment_method')
                            ->label('Payment Method')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'bank_transfer' => 'Bank Transfer',
                                'mbway' => 'MbWay',
                                'paypal' => 'PayPal',
                                default => $state ?? 'Not specified',
                            })
                            ->placeholder('Not specified'),
                        
                        // Bank Transfer Details
                        TextEntry::make('bank_iban')
                            ->label('Bank IBAN')
                            ->placeholder('Not specified')
                            ->visible(fn ($record) => $record->payment_method === 'bank_transfer'),
                        TextEntry::make('account_holder')
                            ->label('Account Holder')
                            ->placeholder('Not specified')
                            ->visible(fn ($record) => $record->payment_method === 'bank_transfer'),
                        TextEntry::make('reference_number')
                            ->label('Transaction Reference')
                            ->placeholder('Not specified')
                            ->visible(fn ($record) => $record->payment_method === 'bank_transfer'),
                        
                        // MbWay Details
                        TextEntry::make('mbway_phone')
                            ->label('MbWay Phone')
                            ->placeholder('Not specified')
                            ->visible(fn ($record) => $record->payment_method === 'mbway'),
                        TextEntry::make('mbway_reference')
                            ->label('MbWay Transaction ID')
                            ->placeholder('Not specified')
                            ->visible(fn ($record) => $record->payment_method === 'mbway'),
                        
                        // PayPal Details
                        TextEntry::make('paypal_email')
                            ->label('PayPal Email')
                            ->placeholder('Not specified')
                            ->visible(fn ($record) => $record->payment_method === 'paypal'),
                        TextEntry::make('paypal_transaction_id')
                            ->label('PayPal Transaction ID')
                            ->placeholder('Not specified')
                            ->visible(fn ($record) => $record->payment_method === 'paypal'),
                        
                        TextEntry::make('balance_after')
                            ->label('Account Balance After')
                            ->money('EUR')
                            ->color(fn ($state) => $state >= 0 ? 'success' : 'danger'),
                    ])
                    ->columns(2),
                    
                Section::make('Related Information')
                    ->schema([
                        TextEntry::make('invoice.invoice_number')
                            ->label('Related Invoice')
                            ->placeholder('No related invoice'),
                        TextEntry::make('createdBy.name')
                            ->label('Processed By')
                            ->placeholder('System'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}