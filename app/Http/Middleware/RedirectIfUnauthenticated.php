<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectIfUnauthenticated
{
    public function handle(Request $request, Closure $next, $guard = 'seller')
    {
        // TEMPORARY DEBUG - REMOVE AFTER FIXING
        // Let's bypass auth check for debugging and see what happens
        if ($request->path() === 'seller/pos' || $request->path() === 'seller/debug-auth') {
            Log::info('Bypassing auth check for debugging', [
                'path' => $request->path(),
                'guard' => $guard,
                'is_authenticated' => Auth::guard($guard)->check(),
                'user_id' => Auth::guard($guard)->id(),
                'tenant' => tenant()?->id,
                'tenant_data' => tenant()?->data,
                'host' => request()->getHost(),
                'full_url' => request()->fullUrl(),
                'session_has_seller_login' => session()->has('login_seller_'.sha1('seller')),
                'all_sessions' => array_keys(session()->all()),
                'central_domains' => config('tenancy.central_domains'),
            ]);

            // Check if we're on a tenant domain
            $isCentralDomain = in_array(request()->getHost(), config('tenancy.central_domains'));
            if ($isCentralDomain) {
                Log::error('Accessing seller routes from central domain', [
                    'host' => request()->getHost(),
                    'should_be_tenant_domain' => true,
                ]);

                return redirect('https://your-tenant-subdomain.'.request()->getHost().$request->getRequestUri());
            }

            // Temporarily allow access for debugging
            return $next($request);
        }

        // Debug information - remove after fixing
        Log::info('RedirectIfUnauthenticated Debug', [
            'guard' => $guard,
            'is_authenticated' => Auth::guard($guard)->check(),
            'user_id' => Auth::guard($guard)->id(),
            'session_data' => session()->all(),
            'tenant' => tenant()?->id,
            'domain' => request()->getHost(),
            'url' => request()->url(),
        ]);

        // Try to get user from both guard and Filament auth
        $guardUser = Auth::guard($guard)->user();
        $filamentUser = null;

        // If we have a Filament panel, try to get user from there too
        if (Filament::getCurrentOrDefaultPanel()) {
            try {
                $filamentUser = Filament::auth()->user();
            } catch (Exception $e) {
                Log::info('Filament auth error', ['error' => $e->getMessage()]);
            }
        }

        // Check if user is authenticated via either method
        if (! $guardUser && ! $filamentUser) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                    'errors' => [],
                ], 401);
            }

            // Redirect to seller login if not authenticated
            return redirect()->route('filament.seller.auth.login');
        }

        // Get the authenticated user (prefer guard user, fallback to filament user)
        $user = $guardUser ?: $filamentUser;

        // If we have a current Filament panel, check panel access
        if (Filament::getCurrentOrDefaultPanel()) {
            $panel = Filament::getCurrentOrDefaultPanel();

            // Check if user can access the current panel
            if ($user instanceof FilamentUser && ! $user->canAccessPanel($panel)) {
                abort(403, 'Access denied to this panel.');
            }
        }

        return $next($request);
    }
}
