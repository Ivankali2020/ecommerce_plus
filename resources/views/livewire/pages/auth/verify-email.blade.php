<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="min-h-screen flex items-center animate-fade-in justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <a href="/">
                @if ($tenantSellerLogo)
                    <img class="mx-auto h-20 w-auto" src="{{ $tenantSellerLogo }}" alt="{{  $tenantSellerName }}">
                    @else
                    <h1 class="text-6xl font-bold font-bebas text-center ">{{  $tenantSellerName  }}</h1>
                @endif
            </a>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white ">
                {{ __('messages.auth.verify_email.title') }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 ">
                {{ __('messages.auth.verify_email.description') }}
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 ">
                {{ __('messages.auth.verify_email.link_sent') }}
            </div>
        @endif

        <div class="flex items-center justify-between space-x-4">
            <x-primary-button wire:click="sendVerification" class="w-full justify-center ">
                {{ __('messages.auth.verify_email.resend_button') }}
            </x-primary-button>

            <button wire:click="logout" type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 dark:focus:ring-offset-gray-800 ">
                {{ __('messages.auth.verify_email.logout_button') }}
            </button>
        </div>
    </div>
</div>
