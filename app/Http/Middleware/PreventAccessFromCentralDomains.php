<?php

namespace App\Http\Middleware;

use App\Support\CentralDomains;
use Closure;
use Illuminate\Http\Request;

/**
 * Custom replacement for Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains.
 *
 * stancl's version reads from config('tenancy.central_domains'), which is baked
 * into the config cache at build time when Railway env vars are not yet injected.
 * This version calls CentralDomains::resolve() directly so the domain list is
 * always evaluated against the live OS environment (via getenv()), bypassing
 * any stale cached config.
 */
class PreventAccessFromCentralDomains
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (in_array($request->getHost(), CentralDomains::resolve(), true)) {
            abort(404);
        }

        return $next($request);
    }
}
