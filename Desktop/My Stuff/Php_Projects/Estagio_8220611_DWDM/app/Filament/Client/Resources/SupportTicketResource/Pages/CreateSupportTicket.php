<?php

namespace App\Filament\Client\Resources\SupportTicketResource\Pages;

use App\Filament\Client\Resources\SupportTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSupportTicket extends CreateRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['client_id'] = auth()->id();
        $data['status'] = 'open';
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}