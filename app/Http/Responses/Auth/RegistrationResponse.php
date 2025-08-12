<?php

namespace App\Http\Responses\Auth;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;
use Illuminate\Http\RedirectResponse;

class RegistrationResponse implements RegistrationResponseContract
{
    public function toResponse($request)
    {
        // Get the domain from the session
        $domain = session('registration_domain');

        if ($domain) {
            // Clear the session data
            session()->forget('registration_domain');

            // Using secure protocol and correct path format
            $subdomain = $domain . '.'.config('tenancy.central_domains')[0].'/seller';
            
            return redirect()->to('http://' . $subdomain);
        }

        // Fallback to default redirect if no domain
        return redirect()->intended(filament()->getUrl());
    }
}
