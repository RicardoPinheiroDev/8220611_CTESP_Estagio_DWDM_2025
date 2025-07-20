<?php

namespace App\Filament\Client\Resources\NotificationResource\Pages;

use App\Filament\Client\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}