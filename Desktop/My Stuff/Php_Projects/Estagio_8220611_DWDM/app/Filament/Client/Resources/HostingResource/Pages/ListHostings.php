<?php

namespace App\Filament\Client\Resources\HostingResource\Pages;

use App\Filament\Client\Resources\HostingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHostings extends ListRecords
{
    protected static string $resource = HostingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}