<?php

namespace App\Filament\Client\Resources\DomainResource\Pages;

use App\Filament\Client\Resources\DomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewDomain extends ViewRecord
{
    protected static string $resource = DomainResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Domain Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Domain Name'),
                        TextEntry::make('registered_at')
                            ->label('Registration Date')
                            ->date(),
                        TextEntry::make('expires_at')
                            ->label('Expiration Date')
                            ->date()
                            ->color(fn ($record) => $record->expires_at->isPast() ? 'danger' : ($record->expires_at->diffInDays() < 30 ? 'warning' : 'success')),
                        TextEntry::make('registrar.name')
                            ->label('Registrar'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'expired' => 'danger',
                                'cancelled' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('payment_status')
                            ->label('Payment Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'unpaid' => 'danger',
                                'pending' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('next_renewal_price')
                            ->label('Next Renewal Price')
                            ->money('EUR'),
                        TextEntry::make('is_managed')
                            ->label('Managed by Us')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                            ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                    ])
                    ->columns(2),
                Section::make('Additional Information')
                    ->schema([
                        TextEntry::make('server.name')
                            ->label('Server')
                            ->placeholder('Not assigned'),
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('No notes'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}