<?php

namespace App\Filament\Client\Resources\FAQResource\Pages;

use App\Filament\Client\Resources\FAQResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewFAQ extends ViewRecord
{
    protected static string $resource = FAQResource::class;
    
    protected static ?string $breadcrumb = null;
    
    protected static ?string $title = 'Perguntas Frequentes';

    public function mount(int | string $record = null): void
    {
        $this->record = Auth::guard('client')->user();
        
        static::authorizeResourceAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Perguntas Frequentes (FAQ)';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}