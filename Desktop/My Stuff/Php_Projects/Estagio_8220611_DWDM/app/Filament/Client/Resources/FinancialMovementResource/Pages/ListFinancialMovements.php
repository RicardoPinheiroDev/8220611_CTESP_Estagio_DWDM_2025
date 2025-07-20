<?php

namespace App\Filament\Client\Resources\FinancialMovementResource\Pages;

use App\Filament\Client\Resources\FinancialMovementResource;
use Filament\Resources\Pages\ListRecords;

class ListFinancialMovements extends ListRecords
{
    protected static string $resource = FinancialMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Financial Movements';
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}