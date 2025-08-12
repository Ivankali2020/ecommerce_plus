<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SellerAuth
{
    public function handle(Request $request, Closure $next)
    {
        // First check if user is authenticated with seller guard
        if (Auth::guard('seller')->check()) {
            return $next($request);
        }

        // If not authenticated with seller guard, check Filament auth
        try {
            // Set the current panel to seller
            $sellerPanel = Filament::getPanel('seller');
            if ($sellerPanel) {
                Filament::setCurrentPanel($sellerPanel);

                // Check if user is authenticated in Filament
                if (Filament::auth()->check()) {
                    $filamentUser = Filament::auth()->user();

                    // Log the user into the seller guard using the Filament user
                    Auth::guard('seller')->login($filamentUser);

                    return $next($request);
                }
            }
        } catch (\Exception $e) {
            Log::error('SellerAuth middleware error: ' . $e->getMessage());
        }

        // If neither authentication method works, redirect to seller login
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        return redirect()->route('filament.seller.auth.login');
    }
}
