<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationGroup = 'Home';

    public function getTitle(): string
    {
        return 'Dashboard';
    }
    
    public function getHeading(): string
    {
        $user = auth()->user();
        return 'Welcome ' . ($user ? $user->name : 'User') . ' 👋';
    }
}