<?php

namespace App\Filament\Resources\FinancialMovementResource\Pages;

use App\Filament\Resources\FinancialMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinancialMovements extends ListRecords
{
    protected static string $resource = FinancialMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}