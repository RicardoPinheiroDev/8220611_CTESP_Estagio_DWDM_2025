<?php

namespace App\Filament\Team\Resources\SupportTicketResource\Pages;

use App\Filament\Team\Resources\SupportTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupportTickets extends ListRecords
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Team operators cannot create tickets - only view and manage existing ones
        ];
    }
}