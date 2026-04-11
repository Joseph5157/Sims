<?php

namespace App\Providers\Filament;

use App\Filament\Parent\Pages\ParentDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class ParentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->id('parent')
            ->path('parent')
            ->login()
            ->brandName('Parent Portal')
            ->colors([
                'primary' => Color::Teal,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                static fn (): string => '<link rel="stylesheet" href="'.Vite::asset('resources/css/app.css').'">',
                ParentDashboard::class,
            )
            ->pages([
                ParentDashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                PreventAccessFromCentralDomains::class,
                InitializeTenancyByDomain::class,
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

        $resourcesPath = app_path('Filament/Parent/Resources');
        if (is_dir($resourcesPath)) {
            $panel = $panel->discoverResources(in: $resourcesPath, for: 'App\Filament\Parent\Resources');
        }

        $pagesPath = app_path('Filament/Parent/Pages');
        if (is_dir($pagesPath)) {
            $panel = $panel->discoverPages(in: $pagesPath, for: 'App\Filament\Parent\Pages');
        }

        $widgetsPath = app_path('Filament/Parent/Widgets');
        if (is_dir($widgetsPath)) {
            $panel = $panel->discoverWidgets(in: $widgetsPath, for: 'App\Filament\Parent\Widgets');
        }

        return $panel;
    }
}
