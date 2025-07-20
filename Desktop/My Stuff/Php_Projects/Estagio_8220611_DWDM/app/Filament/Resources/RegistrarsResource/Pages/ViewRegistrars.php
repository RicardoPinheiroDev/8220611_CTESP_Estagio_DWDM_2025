<?php

namespace App\Filament\Resources\RegistrarsResource\Pages;

use App\Filament\Resources\RegistrarsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistrars extends ViewRecord
{
    protected static string $resource = RegistrarsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('visit_website')
                ->label('Visit Website')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => $this->record->website)
                ->openUrlInNewTab()
        ];
    }
}
