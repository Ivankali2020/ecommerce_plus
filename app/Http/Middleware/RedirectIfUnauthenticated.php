<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfUnauthenticated  // extends FilamentAuthenticate
{
    // protected function unauthenticated($request, array $guards)
    // {
    //     // dd($request);
    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Unauthenticated.',
    //             'errors' => []
    //         ], 401);
    //     }

    //     return redirect()->route('filament.seller.auth.login');

    // }

    public function handle(Request $request, Closure $next, $guard = 'seller')
    {
        if (! Auth::guard($guard)->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                    'errors' => [],
                ], 401);
            }

            // return redirect(Filament::getLoginUrl());
            return redirect()->route('filament.seller.auth.login');
        }

        $user = Filament::auth()->user();
        $panel = Filament::getCurrentPanel();

        abort_if(
            $user instanceof FilamentUser ?
                (! $user->canAccessPanel($panel)) :
                (config('app.env') !== 'local'),
            403,
        );

        return $next($request);

    }
}
