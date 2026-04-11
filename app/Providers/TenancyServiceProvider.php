<?php

namespace App\Providers;

use App\Http\Middleware\InitializeTenancyForLivewireRequests;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Events\TenancyEnded;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Events\TenantCreated;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;

class TenancyServiceProvider extends ServiceProvider
{
    public static string $controllerNamespace = '';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->bootEvents();
        $this->mapRoutes();
        $this->makeTenancyMiddlewareHighestPriority();
        $this->ensureLivewireRequestsAreTenantAware();

        // Spatie Permission caches permissions by a single cache key.
        // In multi-database tenancy, we must clear that cache when tenancy
        // switches so one tenant's cached permissions can't bleed into another.
        $forget = function (): void {
            $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
        };

        Event::listen(TenantCreated::class, function (TenantCreated $event): void {
            // Create the tenant database immediately so new tenants can be migrated and seeded.
            CreateDatabase::dispatchSync($event->tenant);
        });

        Event::listen(TenancyInitialized::class, $forget);
        Event::listen(TenancyEnded::class, $forget);
    }

    protected function bootEvents(): void
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof JobPipeline) {
                    $listener = $listener->toListener();
                }

                Event::listen($event, $listener);
            }
        }
    }

    protected function events(): array
    {
        return [
            Events\CreatingTenant::class => [],
            TenantCreated::class => [],
            Events\SavingTenant::class => [],
            Events\TenantSaved::class => [],
            Events\UpdatingTenant::class => [],
            Events\TenantUpdated::class => [],
            Events\DeletingTenant::class => [],
            Events\TenantDeleted::class => [],

            Events\CreatingDomain::class => [],
            Events\DomainCreated::class => [],
            Events\SavingDomain::class => [],
            Events\DomainSaved::class => [],
            Events\UpdatingDomain::class => [],
            Events\DomainUpdated::class => [],
            Events\DeletingDomain::class => [],
            Events\DomainDeleted::class => [],

            Events\DatabaseCreated::class => [],
            Events\DatabaseMigrated::class => [],
            Events\DatabaseSeeded::class => [],
            Events\DatabaseRolledBack::class => [],
            Events\DatabaseDeleted::class => [],

            Events\InitializingTenancy::class => [],
            TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class,
            ],
            Events\EndingTenancy::class => [],
            TenancyEnded::class => [
                Listeners\RevertToCentralContext::class,
            ],
            Events\BootstrappingTenancy::class => [],
            Events\TenancyBootstrapped::class => [],
            Events\RevertingToCentralContext::class => [],
            Events\RevertedToCentralContext::class => [],
        ];
    }

    protected function mapRoutes(): void
    {
        $this->app->booted(function (): void {
            if (file_exists(base_path('routes/tenant.php'))) {
                Route::namespace(static::$controllerNamespace)
                    ->group(base_path('routes/tenant.php'));
            }
        });
    }

    protected function makeTenancyMiddlewareHighestPriority(): void
    {
        $tenancyMiddleware = [
            InitializeTenancyForLivewireRequests::class,
            Middleware\PreventAccessFromCentralDomains::class,
            Middleware\InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $this->app[Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }

    protected function ensureLivewireRequestsAreTenantAware(): void
    {
        if (! $this->app->bound('livewire')) {
            return;
        }

        $this->app->booted(function (): void {
            foreach (Route::getRoutes()->getRoutes() as $route) {
                $name = $route->getName();

                if (! is_string($name) || $name === '') {
                    continue;
                }

                if (! str($name)->contains('livewire.')) {
                    continue;
                }

                $route->middleware(InitializeTenancyForLivewireRequests::class);
            }
        });
    }
}
