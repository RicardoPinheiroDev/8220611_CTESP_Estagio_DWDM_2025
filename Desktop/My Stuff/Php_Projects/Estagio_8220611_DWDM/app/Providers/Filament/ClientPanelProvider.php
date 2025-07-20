<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ClientOnly;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('client')
            ->login()
            ->registration()
            ->emailVerification()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandName('Client Panel')
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\\Filament\\Client\\Resources')
            ->navigationGroups([
                'Account',
                'Notifications',
                'Hosting',
                'Domains',
                'Support',
            ])
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\\Filament\\Client\\Pages')
            ->pages([
                
            ])
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\\Filament\\Client\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Client\Widgets\FinancialMovementsWidget::class,
            ])
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make()
                    ->label('Profile')
                    ->url('#') 
                    ->icon('heroicon-o-user'),
                \Filament\Navigation\MenuItem::make()
                    ->label('Settings')
                    ->url('#') 
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                ClientOnly::class,
            ])
            ->authGuard('client')
            ->tenant(null);
    }
}