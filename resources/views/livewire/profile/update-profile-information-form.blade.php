<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Filament\Notifications\Notification;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
        Notification::make()->title(__('messages.profile.verification_sent'))
            ->success()
            ->send();
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 ">
            {{ __('messages.profile.title') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 ">
            {{ __('messages.profile.description') }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('messages.profile.name')" class="" />
            <x-text-input wire:model="name" id="name" name="name" type="text"
                class="mt-1 block w-full "
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('messages.profile.email')" class="" />
            <x-text-input wire:model="email" id="email" name="email" type="email"
                class="mt-1 block w-full "
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200 ">
                        {{ __('messages.profile.unverified_email') }}

                        <button wire:click.prevent="sendVerification"
                            wire:loading.attr="disabled"
                            {{-- wire:loading.class="opacity-70 cursor-wait" --}}
                            class="underline text-sm text-gray-600 dark:text-gray-400 mx-3  hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none ring-0 focus:ring-offset-0 focus:ring-0 dark:focus:ring-offset-gray-800 ">
                            <span wire:loading.remove wire:target="sendVerification">
                                {{ __('messages.profile.resend_verification') }}
                            </span>
                            <span wire:loading wire:target="sendVerification" class="flex gap-3 items-center ">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <span wire:loading wire:target="sendVerification">
                                {{ __('messages.profile.sending') }}
                            </span>
                        </button>

                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400 ">
                            {{ __('messages.profile.verification_sent') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="">
                {{ __('messages.profile.save') }}
            </x-primary-button>

            <x-action-message class="me-3 " on="profile-updated">
                {{ __('messages.profile.saved') }}
            </x-action-message>
        </div>
    </form>
</section>
