<?php

declare(strict_types = 1);

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Profile;
use App\Filament\Resources\UserResource;
use Boquizo\FilamentLogViewer\FilamentLogViewerPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Widgets;
use Hexters\HexaLite\HexaLite;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rmsramos\Activitylog\ActivitylogPlugin;

class DashboardPanelProvider extends PanelProvider
{
    /**
     * Boot the panel.
     *
     * Registers a custom JavaScript asset for the Filament panel.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Js::make('custom-script', asset('js/custom.js')),
        ]);

    }

    /**
     * Configure the Filament panel.
     *
     * Defines the panel's settings and components.
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login(Login::class)
            // ->spa()
            ->registration(Register::class)
            ->unsavedChangesAlerts()
            ->passwordReset()
            ->databaseTransactions()
            ->emailVerification()
            // ->profile()
            // ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Amber,
                'blacklist' => '#374151',
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                Profile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->userMenuItems([
                MenuItem::make()
                    ->label('My Profile')
                    ->icon('heroicon-s-user')
                    ->url(fn (): string => Profile::getUrl())
                    ->visible(fn (): bool => ! Auth::user()?->can(UserResource::getModelLabel() . '.withoutGlobalScope')),
            ])
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                EnsureEmailIsVerified::class,
                Authenticate::class,
            ])
            ->plugins([
                HexaLite::make(),
                ActivitylogPlugin::make()
                    ->navigationGroup('Systems')
                    ->authorize(
                        fn () => Auth::user()->can('User.withoutGlobalScope')
                    ),
                FilamentLogViewerPlugin::make()
                    // ->listLogs(\App\Filament\Pages\ListLogs::class)
                    // ->viewLog(\App\Filament\Pages\ViewLog::class)
                    ->navigationGroup('Systems')
                    ->navigationSort(99)
                    ->navigationIcon('heroicon-s-document-text')
                    ->navigationLabel('Log Viewer')
                    ->authorize(
                        fn () => Auth::user()->can('User.withoutGlobalScope')
                    ),
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Master Data'),
                NavigationGroup::make()
                    ->label('Procurement'),
                NavigationGroup::make()
                    ->label('Bidding'),
                NavigationGroup::make()
                    ->label('Location'),
                NavigationGroup::make()
                    ->label('Settings'),
                NavigationGroup::make()
                    ->label('Systems'),
            ]);
        // TODO : make sure aja pake ini yg terbaik atau bukan, atau pake tailwind class nya langsung di blade
        // ->viteTheme('resources/css/custom.css');
    }
}
