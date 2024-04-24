<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->sidebarCollapsibleOnDesktop(true) /* sidebar bisa hide */
            /*             ->id('admin')
            ->path('admin') */ /* alamat path panel admin */

            ->id('admin')
            ->path('dashboard') /* alamat path panel admin = http://localhost/dashboard */

            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->favicon(url: 'images/favicon-mkm.png') /* lokasi fav icon */
            ->darkMode(true) /* untuk mengaktifkan pilihan dark mode */

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                /* Widgets\FilamentInfoWidget::class, */ /* Menghilangkan widget info filamen di panel admin */
            ])
            ->brandName('Manajemen Koleksi Museum') /* untuk page title */
            ->brandLogo(fn () => view('vendor.filament-panels.components.logos')) /* alamat path logo blade */
            ->brandLogoHeight('1.25rem')

            ->navigationGroups([
                'Museum BKT',
                'Museum lain',
            ])

            ->unsavedChangesAlerts()  /* peringatan jika akan meninggalkan halaman tertentu yg berubah inputnya */

            /* ->databaseNotifications() */

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
            ]);
    }
}
