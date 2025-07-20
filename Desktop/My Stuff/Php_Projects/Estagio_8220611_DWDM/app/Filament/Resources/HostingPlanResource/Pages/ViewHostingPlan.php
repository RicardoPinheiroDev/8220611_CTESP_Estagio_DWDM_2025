<?php

namespace App\Filament\Resources\HostingPlanResource\Pages;

use App\Filament\Resources\HostingPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHostingPlan extends ViewRecord
{
    protected static string $resource = HostingPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
