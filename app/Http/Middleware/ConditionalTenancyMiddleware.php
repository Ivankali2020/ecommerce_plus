<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

class ConditionalTenancyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Only apply tenancy middleware if not on central domain
        $isCentralDomain = in_array($request->getHost(), config('tenancy.central_domains'));

        if (!$isCentralDomain) {
            return app(InitializeTenancyByDomain::class)->handle($request, $next);
        }

        return $next($request);
    }
}
