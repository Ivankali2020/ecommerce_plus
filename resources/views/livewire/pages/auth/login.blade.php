<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('profile', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex items-center animate-fade-in  justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <a href="/">
                <img class="mx-auto h-20 w-auto" src="{{ asset('assets/logo.png') }}" alt="Your Logo">
            </a>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.login_page.title') }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.login_page.subtitle') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Login Form -->
        <form wire:submit="login" class="mt-8 space-y-6">
            <div class="rounded-md  space-y-4">
                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('messages.auth.login_page.email')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="form.email" id="email"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                        type="email" name="email" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('messages.auth.login_page.password')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="form.password" id="password"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm"
                        type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <!-- Remember Me -->
                <div class="flex items-center">
                    <input wire:model="form.remember" id="remember" type="checkbox"
                        class="h-4 w-4 text-slate-600 focus:ring-slate-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-300 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.auth.login_page.remember') }}
                    </label>
                </div>

                <!-- Forgot Password -->
                @if (Route::has('password.request'))
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" wire:navigate
                            class="font-medium text-slate-600 hover:text-slate-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                            {{ __('messages.auth.login_page.forgot_password') }}
                        </a>
                    </div>
                @endif
            </div>

            <div>
               
                <div>
                    <x-primary-button wire:loading.attr="disabled" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 focus:outline-none focus:ring-0 ring-none outline-none focus:ring-offset-0 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg wire:loading.remove class="h-5 w-5 text-slate-500 group-hover:text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                            <x-filament::loading-indicator wire:loading class="h-5 w-5 text-slate-500" />
                        </span>
                        <span wire:loading.remove>{{ __('messages.auth.login_page.login_button') }}</span>
                        <span wire:loading>{{ __('messages.auth.login_page.logging_in') }}</span>
                    </x-primary-button>
                </div>
            </div>

            <!-- Register Link -->
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.login_page.no_account') }}
                <a href="{{ route('register') }}" wire:navigate class="font-medium text-slate-600 hover:text-slate-500">
                    {{ __('messages.auth.login_page.register') }}
                </a>
            </p>
        </form>
    </div>
</div>
