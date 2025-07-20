<?php

namespace App\Filament\Resources\RenewalsResource\Pages;

use App\Filament\Resources\RenewalsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRenewals extends ListRecords
{
    protected static string $resource = RenewalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
