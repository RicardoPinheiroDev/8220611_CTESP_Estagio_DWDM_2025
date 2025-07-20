<?php

namespace App\Filament\Client\Resources\ProfileResource\RelationManagers;

use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';
    protected static ?string $recordTitleAttribute = 'invoice_number';
    protected static ?string $title = 'Invoices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'overdue',
                    ]),
                TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ]),
            ])

            ->actions([
                Action::make('download_invoice')
                    ->label('Download Invoice')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Invoice $record) => $record->invoice_file ? asset('storage/' . $record->invoice_file) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Invoice $record) => $record->invoice_file),
                Action::make('download_receipt')
                    ->label('Download Receipt')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (Invoice $record) => $record->receipt_file ? asset('storage/' . $record->receipt_file) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Invoice $record) => $record->receipt_file),
            ])

            ->defaultSort('created_at', 'desc');
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}