<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('profile', absolute: false), navigate: true);
    }
}; ?>


<div class="min-h-screen flex items-center animate-fade-in justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo and Header -->
        <div class="text-center">
            <a href="/">
                <img class="mx-auto h-20 w-auto" src="{{ asset('assets/logo.png') }}" alt="Your Logo">
            </a>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.register_page.title') }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.register_page.subtitle') }}
            </p>
        </div>

        <!-- Registration Form -->
        <form wire:submit="register" class="mt-8 space-y-6">
            <div class="rounded-md space-y-4">
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('messages.auth.register_page.name')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="name" id="name" type="text"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                        required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('messages.auth.register_page.email')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="email" id="email" type="email"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                        required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('messages.auth.register_page.password')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="password" id="password" type="password"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('messages.auth.register_page.confirm_password')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <!-- Register Button -->
            <div>
                <x-primary-button wire:loading.attr="disabled" class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" wire:loading.remove class="h-5 w-5 text-slate-500 group-hover:text-slate-400" >
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z" clip-rule="evenodd" />
                          </svg>
                        <x-filament::loading-indicator wire:loading class="h-5 w-5 text-slate-500" />
                    </span>
                    <span wire:loading.remove>{{ __('messages.auth.register_page.register_button') }}</span>
                    <span wire:loading>{{ __('messages.auth.register_page.registering') }}</span>
                </x-primary-button>
            </div>

            <!-- Login Link -->
            <p class="mt-2 text-end text-sm text-gray-600 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.register_page.have_account') }}
                <a href="{{ route('login') }}" wire:navigate class="font-medium text-slate-600 hover:text-slate-500">
                    {{ __('messages.auth.register_page.login') }}
                </a>
            </p>
            
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
        </form>
    </div>
</div>
