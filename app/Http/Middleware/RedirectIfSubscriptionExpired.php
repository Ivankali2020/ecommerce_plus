<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSubscriptionExpired
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('seller')->check() && session('subscription_expired')) {
            // Clear the session flag
            session()->forget('subscription_expired');
            
            // Only redirect if not already on the subscription page
            if (!$request->routeIs('seller.subscription.*')) {
                return redirect()->route('seller.subscription.plans');
            }
        }

        return $next($request);
    }
}