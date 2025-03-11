<?php

use IlluminateAuthEventsPasswordReset;
use IlluminateSupportFacadesHash;
use IlluminateSupportFacadesPassword;
use IlluminateSupportFacadesSession;
use IlluminateSupportStr;
use IlluminateValidationRules;
use LivewireAttributesLayout;
use LivewireAttributesLocked;
use LivewireVoltComponent;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="min-h-screen flex items-center animate-fade-in justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <a href="/">
                <img class="mx-auto h-16 w-auto" src="{{ asset('assets/logo.png') }}" alt="Your Logo">
            </a>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.reset_password.title') }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.reset_password.description') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="resetPassword" class="mt-8 space-y-6">
            <div class="rounded-md shadow-sm space-y-4">
                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('messages.reset_password.email')" 
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="email" id="email" 
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                        type="email" name="email" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('messages.reset_password.password')" 
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="password" id="password" 
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                        type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('messages.reset_password.confirm_password')" 
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" 
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                        type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-primary-button class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 focus:outline-none focus:ring-0 ring-none outline-none focus:ring-offset-0 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-slate-500 group-hover:text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    {{ __('messages.reset_password.button') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
