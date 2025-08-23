<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Stancl\Tenancy\Database\Models\Domain;

class SocialAuthController extends Controller
{
    /**
     * Generate dynamic Google callback URL based on current domain
     */
    private function getDynamicCallbackUrl(): string
    {
        $scheme = request()->isSecure() ? 'https' : 'http';
        $host = request()->getHost();

        // Handle different environments
        if (app()->environment('production')) {
            // In production, ensure HTTPS
            $scheme = 'https';
        }

        return "{$scheme}://{$host}/google/callback";
    }

    public function redirectToGoogle()
    {
        // Set dynamic redirect URL based on current domain
        $redirectUrl = $this->getDynamicCallbackUrl();

        // Temporarily override the config for this request
        config(['services.google.redirect' => $redirectUrl]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Set the same dynamic redirect URL for the callback
            $redirectUrl = $this->getDynamicCallbackUrl();
            config(['services.google.redirect' => $redirectUrl]);

            $googleUser = Socialite::driver('google')->user();

            // Find existing user or create new one
            $user = User::where('email', $googleUser->email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
            } else {
                // Update existing user with Google ID if missing
                if (! $user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            // attach photo to profile_photo
            if ($googleUser->avatar) {
                // check have profile photo already
                if (! $user->hasMedia('profile_photo')) {
                    $user->addMediaFromUrl($googleUser->avatar)->toMediaCollection('profile_photo');
                }
            }

            // Log the user in
            Auth::login($user);

            Notification::make()
                ->title('Google Authentication Successful')
                ->body('You have successfully logged in with Google.')
                ->success()
                ->send();

            // Redirect to welcome page
            return redirect()->route('welcome');

        } catch (Exception $e) {
            // Handle errors
            Notification::make()->title('Google Authentication Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return redirect()->route('login');
        }
    }
}
