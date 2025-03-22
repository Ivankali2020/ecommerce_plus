<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public string $cfTurnstileResponse;
    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate([
            'cfTurnstileResponse' => ['required', 'turnstile'],
        ]);

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
                    <x-primary-button wire:loading.attr="disabled" class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 focus:outline-none focus:ring-0 ring-none outline-none focus:ring-offset-0 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
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

            <div> 
                <div wire:ignore class="text-center rounded " x-data >
                    <x-turnstile wire:model="cfTurnstileResponse"
                    data-action="login"
                    data-cdata="sessionid-dsd"
                    data-size="flexible" data-theme="light" data-tabindex="1" />
                </div>
                <x-input-error :messages="$errors->get('cfTurnstileResponse')" class="mt-2" />
            </div>

            <!-- Register Link -->
            <p class="mt-2 text-end text-sm text-gray-600 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.login_page.no_account') }}
                <a href="{{ route('register') }}"  class="font-medium text-slate-600 hover:text-slate-500">
                    {{ __('messages.auth.login_page.register') }}
                </a>
            </p>
        </form>

        <!-- Social Login Divider -->
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.auth.register_page.or_continue_with') }}
                    </span>
                </div>
            </div>
            
            <!-- Google Login Button -->
            <div class="mt-6">
                <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                        <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                            <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
                            <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
                            <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
                            <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
                        </g>
                    </svg>
                    {{ __('messages.auth.register_page.sign_in_with_google') }}
                </a>
            </div>
        </div>
    </div>
</div>
