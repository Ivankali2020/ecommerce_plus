<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CentralDomainOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if we're in a tenant context
        if (tenant()) {
            abort(404, 'Admin panel is only available on the central domain.');
        }

        $centralDomains = config('tenancy.central_domains', []);
        $currentHost = $request->getHost();

        // Check if current domain is in central domains list
        if (! in_array($currentHost, $centralDomains)) {
            // Redirect to central domain admin panel
            $centralDomain = $centralDomains[0] ?? 'localhost';
            $protocol = $request->isSecure() ? 'https' : 'http';

            return redirect()->to("{$protocol}://{$centralDomain}/oh_my_admin");
        }

        return $next($request);
    }
}
