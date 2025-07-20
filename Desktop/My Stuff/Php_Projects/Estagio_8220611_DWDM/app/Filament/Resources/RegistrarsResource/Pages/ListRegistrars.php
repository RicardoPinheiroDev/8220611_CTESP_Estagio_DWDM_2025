<?php

namespace App\Filament\Resources\RegistrarsResource\Pages;

use App\Filament\Resources\RegistrarsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrars extends ListRecords
{
    protected static string $resource = RegistrarsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
