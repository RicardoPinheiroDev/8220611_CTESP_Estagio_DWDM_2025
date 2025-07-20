<?php

namespace App\Filament\Client\Resources\ProfileResource\Pages;

use App\Filament\Client\Resources\ProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;
    
    protected static ?string $breadcrumb = null;

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

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Profile updated successfully';
    }
}