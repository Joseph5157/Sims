<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\AttendanceReport;
use App\Filament\Faculty\Pages\FacultyTimetable;
use App\Filament\Faculty\Resources\AttendanceResource;
use App\Filament\Faculty\Widgets\AttendanceChartWidget;
use App\Filament\Faculty\Widgets\FacultyStatsWidget;
use App\Filament\Faculty\Widgets\TodayScheduleWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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

class FacultyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('faculty')
            ->path('faculty')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName('Faculty Portal')
            ->discoverResources(in: app_path('Filament/Faculty/Resources'), for: 'App\Filament\Faculty\Resources')
            ->discoverPages(in: app_path('Filament/Faculty/Pages'), for: 'App\Filament\Faculty\Pages')
            ->pages([
                Dashboard::class,
                FacultyTimetable::class,
                AttendanceReport::class,
            ])
            // Force registration of AttendanceResource
            ->resources([
                AttendanceResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Faculty/Widgets'), for: 'App\Filament\Faculty\Widgets')
            ->widgets([
                FacultyStatsWidget::class,
                AttendanceChartWidget::class,
                TodayScheduleWidget::class,
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
    }
}
