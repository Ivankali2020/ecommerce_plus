<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Filament\Notifications\Notification;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Find existing user or create new one
            $user = User::firstOrCreate(
                ['email' => $googleUser->email],
                [
                    'google_id' => $googleUser->id,
                    'name' => $googleUser->name,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(), // Google accounts are already verified
                ]
            );
            
            // Log the user in
            Auth::login($user);
            
            // Redirect to profile page
            return redirect()->route('profile');
            
        } catch (\Exception $e) {
            // Handle errors
            Notification::make()->title('Google Authentication Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return redirect()->route('login');
        }
    }
}