<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\AttendanceDefaulters;
use App\Filament\Admin\Pages\AttendanceReport;
use App\Filament\Admin\Pages\GradebookReport;
use App\Filament\Admin\Pages\StudentReportCard;
use App\Filament\Admin\Widgets\AttendanceOverviewWidget;
use App\Filament\Admin\Widgets\RecentActivityWidget;
use App\Filament\Admin\Widgets\StatsOverviewWidget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
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
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class AdminPanelProvider extends PanelProvider
{
    protected static function renderCompiledStylesheetLink(): string
    {
        $manifestPath = public_path('build/manifest.json');

        if (! file_exists($manifestPath)) {
            return '';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $cssFile = $manifest['resources/css/app.css']['file'] ?? null;

        if (! is_string($cssFile) || $cssFile === '') {
            return '';
        }

        return '<link rel="stylesheet" href="'.asset('build/assets/'.basename($cssFile)).'">';
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->plugin(FilamentShieldPlugin::make())
            ->colors([
                'primary' => Color::Amber,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => static::renderCompiledStylesheetLink(),
            )
            ->brandName('Admin Panel')
            ->pages([
                Dashboard::class,
                GradebookReport::class,
                AttendanceReport::class,
                StudentReportCard::class,
                AttendanceDefaulters::class,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverviewWidget::class,
                AttendanceOverviewWidget::class,
                RecentActivityWidget::class,
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                // Ensure tenant-scoped panels always run inside tenant context,
                // and cannot be accessed from central domains (which have no tenant users table).
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
    }
}
