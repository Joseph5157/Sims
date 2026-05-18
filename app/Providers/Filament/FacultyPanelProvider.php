<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\AttendanceReport;
use App\Filament\Faculty\Pages\AttendanceGrid;
use App\Filament\Faculty\Pages\AttendanceHistory;
use App\Filament\Faculty\Pages\FacultyTimetable;
use App\Filament\Faculty\Pages\MarkAttendance;
use App\Filament\Faculty\Pages\MarksEntry;
use App\Filament\Faculty\Pages\MyStudents;
use App\Filament\Faculty\Resources\AttendanceResource;
use App\Filament\Faculty\Widgets\AttendanceChartWidget;
use App\Filament\Faculty\Widgets\FacultyStatsWidget;
use App\Filament\Faculty\Widgets\TodayAttendanceWidget;
use App\Filament\Faculty\Widgets\TodayScheduleWidget;
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

class FacultyPanelProvider extends PanelProvider
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
            ->id('faculty')
            ->path('faculty')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => static::renderCompiledStylesheetLink(),
            )
            ->brandName('Faculty Portal')
            ->discoverResources(in: app_path('Filament/Faculty/Resources'), for: 'App\Filament\Faculty\Resources')
            ->discoverPages(in: app_path('Filament/Faculty/Pages'), for: 'App\Filament\Faculty\Pages')
            ->pages([
                Dashboard::class,
                MarkAttendance::class,
                MarksEntry::class,
                FacultyTimetable::class,
                AttendanceGrid::class,
                AttendanceHistory::class,
                MyStudents::class,
                AttendanceReport::class,
            ])
            // Force registration of AttendanceResource
            ->resources([
                AttendanceResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Faculty/Widgets'), for: 'App\Filament\Faculty\Widgets')
            ->widgets([
                TodayAttendanceWidget::class,
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
