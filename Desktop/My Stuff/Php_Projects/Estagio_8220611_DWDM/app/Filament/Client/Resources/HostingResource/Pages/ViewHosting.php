<?php

namespace App\Filament\Client\Resources\HostingResource\Pages;

use App\Filament\Client\Resources\HostingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewHosting extends ViewRecord
{
    protected static string $resource = HostingResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Hosting Information')
                    ->schema([
                        TextEntry::make('account_name')
                            ->label('Account Name'),
                        TextEntry::make('domain.name')
                            ->label('Associated Domain')
                            ->placeholder('No domain assigned'),
                        TextEntry::make('plan.name')
                            ->label('Hosting Plan'),
                        TextEntry::make('starts_at')
                            ->label('Service Started')
                            ->date(),
                        TextEntry::make('expires_at')
                            ->label('Expires')
                            ->date()
                            ->color(fn ($record) => $record->expires_at->isPast() ? 'danger' : ($record->expires_at->diffInDays() < 30 ? 'warning' : 'success')),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'suspended' => 'warning',
                                'cancelled' => 'danger',
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
                    ])
                    ->columns(2),
                Section::make('Technical Information')
                    ->schema([
                        TextEntry::make('server.name')
                            ->label('Server')
                            ->placeholder('Not assigned'),
                        TextEntry::make('server.ip_address')
                            ->label('Server IP')
                            ->placeholder('Not available'),
                        TextEntry::make('plan.storage_limit')
                            ->label('Storage Limit')
                            ->placeholder('Not specified'),
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('No notes'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}