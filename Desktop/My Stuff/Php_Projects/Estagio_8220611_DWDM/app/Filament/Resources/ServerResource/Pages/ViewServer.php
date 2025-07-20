<?php

namespace App\Filament\Resources\ServerResource\Pages;

use App\Filament\Resources\ServerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Http;

class ViewServer extends ViewRecord
{
    protected static string $resource = ServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('ping')
                ->label('Ping Server')
                ->icon('heroicon-o-signal')
                ->action(fn () => $this->pingServer())
                ->color('success')
        ];
    }
    
    protected function pingServer(): void
    {
        try {
            $response = Http::timeout(5)->get('http://' . $this->record->ip_address);
            
            if ($response->successful()) {
                $this->dispatch('ping-success', message: 'Server is responding!');
            } else {
                $this->dispatch('ping-error', message: 'Server returned an error: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->dispatch('ping-error', message: 'Error pinging server: ' . $e->getMessage());
        }
    }
}
