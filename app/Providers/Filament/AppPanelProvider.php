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
            // No ->registration(): volunteers sign up at /join, which collects a
            // postcode and consent and matches them to a bag holder. /app/register
            // redirects there. Staff accounts are created in the panel.
            ->passwordReset()
            // With User implementing MustVerifyEmail, this is what an unverified
            // volunteer sees the moment they sign in: a "confirm your email"
            // prompt with a rate-limited resend button, instead of the panel.
            // The resend runs through User::sendEmailVerificationNotification(),
            // so a volunteer gets the welcome email again rather than a bare
            // verification link.
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
                // Filament aliases Tailwind's whole `gray-*` utility scale onto this
                // registration, so it is the single lever that decides the colour of
                // every neutral surface, border and muted label in the panel.
                // Color::Gray is Tailwind's blue-tinted grey, which fights the site's
                // neutral near-blacks; the dark end below is the ink ramp from
                // resources/css/variables.css verbatim, and the light end is a true
                // neutral carrying only a whisper of the same hue.
                'gray' => [
                    50 => '#fafafa',
                    100 => '#f4f4f5',
                    200 => '#e7e6e9',
                    300 => '#d3d1d6',
                    400 => '#a3a1a8',
                    500 => '#76747c',
                    600 => '#57555d',
                    700 => '#423f47',
                    800 => '#1f1d22',
                    900 => '#141317',
                    950 => '#0b0a0c',
                ],
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
