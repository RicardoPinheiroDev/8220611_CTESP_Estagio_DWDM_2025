<?php

namespace App\Filament\Resources\RenewalsResource\Pages;

use App\Filament\Resources\RenewalsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRenewals extends ViewRecord
{
    protected static string $resource = RenewalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
