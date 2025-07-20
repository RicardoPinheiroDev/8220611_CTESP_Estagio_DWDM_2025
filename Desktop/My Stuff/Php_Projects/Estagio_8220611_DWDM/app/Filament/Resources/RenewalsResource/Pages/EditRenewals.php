<?php

namespace App\Filament\Resources\RenewalsResource\Pages;

use App\Filament\Resources\RenewalsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRenewals extends EditRecord
{
    protected static string $resource = RenewalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
