<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerBelongsToTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if no tenant context
        if (!tenant()) {
            return $next($request);
        }

        // Skip if no authenticated seller
        if (!Auth::guard('seller')->check()) {
            return $next($request);
        }

        $seller = Auth::guard('seller')->user();
        $currentTenantId = tenant('id');

        // Check if the seller belongs to the current tenant using the many-to-many relationship
        if (!$seller->tenants()->where('tenant_id', $currentTenantId)->exists()) {
            // Log the unauthorized access attempt
            Log::warning('Seller access denied: Seller ID ' . $seller->id . ' attempted to access tenant ' . $currentTenantId . ' but is not associated with this tenant', [
                'seller_id' => $seller->id,
                'seller_email' => $seller->email,
                'current_tenant' => $currentTenantId,
                'seller_tenants' => $seller->tenants()->pluck('tenant_id')->toArray(),
                'request_url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
            ]);

            // Log the seller out
            Auth::guard('seller')->logout();

            // Invalidate the session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // For API requests, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized access to this tenant.',
                    'error' => 'TENANT_ACCESS_DENIED'
                ], 403);
            }

            // For web requests, redirect to login with error message
            return redirect()->route('filament.seller.auth.login')
                ->withErrors([
                    'email' => 'You are not authorized to access this tenant. Please login with the correct account for this domain.'
                ])
                ->with('warning', 'Security Alert: Unauthorized tenant access attempt detected.');
        }

        return $next($request);
    }
}
