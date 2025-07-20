<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalClients = Client::count();
        $activeClients = Client::where('is_active', true)->count();
        $inactiveClients = Client::where('is_active', false)->count();
        
        return [
            Stat::make('Total Clients', $totalClients)
                ->description("Active: {$activeClients} | Inactive: {$inactiveClients}")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-r from-green-400 to-blue-500',
                ]),
        ];
    }
}