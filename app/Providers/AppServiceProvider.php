<?php

namespace App\Providers;

use App\Console\Commands\CreateTenant;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateTenant::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);

        // Super admins bypass all gate checks before Spatie Permission queries
        // the roles/permissions tables (which only exist in tenant databases,
        // not the central DB).
        Gate::before(function (\App\Models\User $user) {
            if ($user->is_super_admin) {
                return true;
            }
        });
    }
}
