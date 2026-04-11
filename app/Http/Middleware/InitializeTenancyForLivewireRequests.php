<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Tenancy;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyForLivewireRequests
{
    public function __construct(
        public Tenancy $tenancy,
        public InitializeTenancyByDomain $initializeTenancyByDomain,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->tenancy->initialized) {
            return $next($request);
        }

        $centralDomains = config('tenancy.central_domains', []);

        if (is_array($centralDomains) && in_array($request->getHost(), $centralDomains, true)) {
            return $next($request);
        }

        return $this->initializeTenancyByDomain->handle($request, $next);
    }
}
