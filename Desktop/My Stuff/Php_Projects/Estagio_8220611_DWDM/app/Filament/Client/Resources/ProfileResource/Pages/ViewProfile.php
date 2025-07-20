<?php

namespace App\Filament\Client\Resources\ProfileResource\Pages;

use App\Filament\Client\Resources\ProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ViewProfile extends ViewRecord
{
    protected static string $resource = ProfileResource::class;
    
    protected static ?string $breadcrumb = null;
    
    protected static ?string $title = 'User Profile';

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
        return [
            Actions\Action::make('edit')
                ->label('Edit Profile')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->url(fn (): string => ProfileResource::getUrl('edit')),
        ];
    }

}