<?php

namespace App\Filament\Resources\RegistrarsResource\Pages;

use App\Filament\Resources\RegistrarsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistrars extends CreateRecord
{
    protected static string $resource = RegistrarsResource::class;

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }
}
