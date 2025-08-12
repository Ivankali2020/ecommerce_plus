<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSellerSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('seller')->check()) {
            $seller = Auth::guard('seller')->user();

            // For staff accounts, check parent's subscription
            if ($seller->parent_id) {
                $parent = $seller->parent;
                if (! $parent || $parent->isSubscriptionExpired()) {
                    return redirect()->route('seller.subscription.expired');
                }
            }
            // For main seller accounts, check own subscription
            elseif ($seller->isSubscriptionExpired()) {
                return redirect()->route('seller.subscription.expired');
            }
        }

        return $next($request);
    }
}
