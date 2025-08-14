<div class="min-h-screen flex items-center animate-fade-in justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <!-- Language Switcher -->
    <div class="absolute top-4 right-4">
        <div class="flex space-x-2">
            <a href="{{ route('language.switch', 'en') }}" 
               class="px-3 py-2 text-sm rounded-md transition-colors {{ app()->currentLocale() === 'en' ? 'bg-slate-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                EN
            </a>
            <a href="{{ route('language.switch', 'my') }}" 
               class="px-3 py-2 text-sm rounded-md transition-colors {{ app()->currentLocale() === 'my' ? 'bg-slate-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }} {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                မြန်မာ
            </a>
        </div>
    </div>

    <div class="max-w-md w-full space-y-8">
        <!-- Logo and Header -->
        <div class="text-center">
            <a href="/">
                <img class="mx-auto h-20 w-auto" src="{{ asset('assets/logo.png') }}" alt="Your Logo">
            </a>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.seller_register_page.title') }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.seller_register_page.subtitle') }}
            </p>
        </div>

        <!-- Registration Form -->
        <form wire:submit="register" class="mt-8 space-y-6">
            <div class="rounded-md space-y-4">
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('messages.auth.seller_register_page.name')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="name" id="name" type="text"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm dark:bg-gray-800 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                        required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('messages.auth.seller_register_page.email')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="email" id="email" type="email"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm dark:bg-gray-800"
                        required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Domain -->
                <div>
                    <x-input-label for="domain" :value="__('messages.auth.seller_register_page.subdomain')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <div class="flex">
                        <x-text-input wire:model.live="domain" id="domain" type="text"
                            class="appearance-none rounded-l-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm dark:bg-gray-800"
                            required />
                        <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                            .{{ config("tenancy.central_domains")[0] }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.auth.seller_register_page.subdomain_preview') }} <strong>{{ $domain ?: 'your-store' }}.{{ config("tenancy.central_domains")[0] }}</strong>
                    </p>
                    <x-input-error :messages="$errors->get('domain')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('messages.auth.seller_register_page.password')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="password" id="password" type="password"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm dark:bg-gray-800"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('messages.auth.seller_register_page.confirm_password')"
                        class="mb-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-slate-500 focus:border-slate-500 focus:z-10 sm:text-sm dark:bg-gray-800"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div> 
                    <div wire:ignore class="text-center">
                        <x-turnstile wire:model="cfTurnstileResponse" 
                        data-action="register"
                        data-cdata="{{ uniqid() }}"
                        data-size="flexible" data-theme="light" data-tabindex="1" />
                    </div>
                
                    <x-input-error :messages="$errors->get('cfTurnstileResponse')" class="mt-2" />
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
                    <span wire:loading.remove>{{ __('messages.auth.seller_register_page.register_button') }}</span>
                    <span wire:loading>{{ __('messages.auth.seller_register_page.registering') }}</span>
                </x-primary-button>
            </div>

            <!-- Login Link -->
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.auth.seller_register_page.have_store') }}
                <a href="{{ route('welcome') }}" class="font-medium text-slate-600 hover:text-slate-500">
                    {{ __('messages.auth.seller_register_page.go_to_main') }}
                </a>
            </p>
        </form>
    </div>
</div>
