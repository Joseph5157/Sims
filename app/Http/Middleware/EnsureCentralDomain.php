<?php

namespace App\Http\Middleware;

use App\Support\CentralDomains;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentralDomain
{
    /**
     * Block access to the superadmin panel from tenant domains.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->getHost(), CentralDomains::resolve(), true)) {
            abort(404);
        }

        return $next($request);
    }
}
