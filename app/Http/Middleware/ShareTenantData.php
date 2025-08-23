<?php

namespace App\Http\Middleware;

use App\Models\Seller;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareTenantData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (tenant()) {
            $seller = Cache::remember('tenant_seller_'.tenant('id'), 3600, function () {
                return Seller::where('tenant_id', tenant('id'))->with('media')->first();
            });

            if ($seller) {
                View::share([
                    'tenantSellerLogo' => $seller->getFirstMediaUrl('logos'),
                    'tenantEnableGoogleAuth' => $seller->enabled_google_auth,
                    'tenantSellerName' => $seller->name,
                ]);
            }
        }

        return $next($request);
    }
}
