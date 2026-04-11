<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\FacultyPanelProvider;
use App\Providers\Filament\ParentPanelProvider;
use App\Providers\Filament\StudentPanelProvider;
use App\Providers\Filament\SuperAdminPanelProvider;
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    TenancyServiceProvider::class,
    AdminPanelProvider::class,
    FacultyPanelProvider::class,
    ParentPanelProvider::class,
    StudentPanelProvider::class,
    SuperAdminPanelProvider::class,
];
