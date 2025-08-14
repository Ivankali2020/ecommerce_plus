<?php

namespace App\Http\Middleware;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Exchange;
use App\Models\Payment;
use App\Models\Product;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AssignGlobeScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply tenant scoping when in tenant context
        if ($this->isTenantContext()) {
            $tenantId = tenant('id');

            if ($tenantId) {
                // Apply tenant scoping to all relevant models
                // This ensures tenant isolation even when BelongsToTenant doesn't work with LIKE queries
                Product::addGlobalScope('tenant_scope', function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                });

                Brand::addGlobalScope('tenant_scope', function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                });

                Category::addGlobalScope('tenant_scope', function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                });

                Attribute::addGlobalScope('tenant_scope', function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                });

                Exchange::addGlobalScope('tenant_scope', function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                });

                Payment::addGlobalScope('tenant_scope', function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                });
            }
        }

        return $next($request);
    }

    /**
     * Check if we're in a tenant context (on a tenant domain)
     */
    private function isTenantContext(): bool
    {
        // Check if tenant is initialized (we're on a tenant domain)
        if (tenant()) {
            return true;
        }

        // Additional check: not on central domain
        $centralDomains = config('tenancy.central_domains', []);
        if (!in_array(request()->getHost(), $centralDomains)) {
            return true;
        }

        return false;
    }
}
