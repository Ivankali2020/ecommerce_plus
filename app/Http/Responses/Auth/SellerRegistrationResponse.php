<?php

namespace App\Http\Responses\Auth;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;
use Illuminate\Http\RedirectResponse;

class SellerRegistrationResponse implements RegistrationResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // Get the domain from the session (set during registration)
        $domain = session('registration_domain') ?? session('temp_registration_domain');

        if ($domain) {
            // Clear the session data
            session()->forget(['registration_domain', 'temp_registration_domain']);

            // Redirect to the new tenant domain login page
            $subdomain = $domain . '.' . config('tenancy.central_domains')[0];
            $redirectUrl = 'http://' . $subdomain . '/seller/login';

            return redirect($redirectUrl)->with('success', 'Registration successful! Please login to your new store.');
        }

        // Fallback to default redirect if no domain
        return redirect('/')->with('error', 'Registration completed but redirect failed. Please visit your domain directly.');
    }
}
