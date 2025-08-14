<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="min-h-screen flex items-center animate-fade-in justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <a href="/">
                <img class="mx-auto h-20 w-auto" src="{{ asset('assets/logo.png') }}" alt="Your Logo">
            </a>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.forgot_password.title') }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.forgot_password.subtitle') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="sendPasswordResetLink" class="mt-8 space-y-6">
            <div class="rounded-md space-y-4">
                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('messages.auth.login_page.email')" 
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="email" id="email" 
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                        type="email" name="email" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-primary-button wire:loading.attr="disabled" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 focus:outline-none focus:ring-0 ring-none outline-none focus:ring-offset-0 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg wire:loading.remove class="h-5 w-5 text-slate-500 group-hover:text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2.94 6.412A2 2 0 002 8.108V16a2 2 0 002 2h12a2 2 0 002-2V8.108a2 2 0 00-.94-1.696l-6-3.75a2 2 0 00-2.12 0l-6 3.75zm2.615 2.423a1 1 0 10-1.11 1.664l5 3.333a1 1 0 001.11 0l5-3.333a1 1 0 00-1.11-1.664L10 11.798 5.555 8.835z" clip-rule="evenodd" />
                        </svg>
                        <x-filament::loading-indicator wire:loading class="h-5 w-5 text-slate-500" />
                    </span>
                    <span wire:loading.remove>{{ __('messages.auth.forgot_password.reset_link_button') }}</span>
                    <span wire:loading>{{ __('messages.auth.forgot_password.sending') }}</span>
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
