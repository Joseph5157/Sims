<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\FacultyPanelProvider::class,   // ← Added
    App\Providers\Filament\StudentPanelProvider::class,   // ← Added
];
