<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBelongsToSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $sellerId = $request->sellerId;
        $user = Auth::guard('user')->user();

        if (is_null($user) || $user->seller->id != $sellerId) {
            throw new AuthenticationException;
        }

        return $next($request);
    }
}
