<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->brandName('Litter Free Leeds')
            ->brandLogo(fn (): string => asset('favicon.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(fn (): string => asset('favicon.svg'))
            ->colors([
                // Litter Free Leeds brand scale, mirrored from the @theme block in
                // resources/css/variables.css — keep the two in sync by hand.
                // Passing the explicit shade array (rather than a single hex string)
                // preserves these exact colours; a string would only keep the hue.
                'primary' => [
                    50 => '#fdf2fb',
                    100 => '#fbe1f6',
                    200 => '#f4c3ec',
                    300 => '#e99edd',
                    400 => '#d372c7',
                    500 => '#ba4eae',
                    600 => '#9b4391',
                    700 => '#88337f',
                    800 => '#6b2664',
                    900 => '#551f4f',
                    950 => '#3b1536',
                ],
                'gray' => Color::Gray,
            ])
            ->viteTheme('resources/css/panel.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationSort(99)
                    ->navigationGroup('User Management')
                    ->localizePermissionLabels(),
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
