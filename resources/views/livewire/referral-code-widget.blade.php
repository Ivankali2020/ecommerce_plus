<?php

use App\Models\User;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

new class extends Component
{
    public ?string $referralCode = null;
    public ?string $referralUrl = null;
    public array $stats = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->referralCode = $user->referral_code;
        $this->referralUrl = route('register', ['ref' => $this->referralCode]);
        
        // Cache stats for 5 minutes to improve performance
        $cacheKey = "user_referral_stats_{$user->id}";
        
        $this->stats = Cache::remember($cacheKey, 300, function () use ($user) {
            return [
                'total_referrals' => $user->referrals()->count(),
                'pending_referrals' => $user->referrals()
                    ->whereNull('verified_at')
                    ->count(),
                'points_earned' => $user->referrals()
                    ->whereNotNull('verified_at')
                    ->sum('points_awarded'),
            ];
        });

    }

}; ?>

<div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 md:py-10 space-y-6 mt-4 ">
    <div class="">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('referral.title') }}
            </h2>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ __('referral.description') }}
            </p>
        </header>

        <!-- Stats Grid -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Total Referrals -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('referral.stats.total_referrals') }}
                </dt>
                <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $stats['total_referrals'] }}
                </dd>
            </div>

            <!-- Pending Verifications -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('referral.stats.pending') }}
                </dt>
                <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $stats['pending_referrals'] }}
                </dd>
            </div>

            <!-- Points Earned -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('referral.stats.points_earned') }}
                </dt>
                <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $stats['points_earned'] }}
                </dd>
            </div>
        </div>

        <div class="mt-6 flex flex-col md:flex-row items-center gap-3">
            <!-- Referral Code -->
            <div >
                <x-input-label for="referral-code" value="{{ __('referral.code.label') }}" />
                <div class="mt-2 flex">
                    <span class="block w-full border bg-gray-50  border-gray-300 rounded  px-4 py-1.5 ">{{ $referralCode }}</span>
                    <button type="button"
                        x-data="{ copied: false }"
                        @click="
                            navigator.clipboard.writeText('{{ $referralCode }}');
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        "
                        class="ml-3 p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                    >
                        <span x-show="!copied">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                            </svg>
                        </span>
                        <span x-show="copied" x-cloak>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Share Link -->
            <div>
                <x-input-label for="referral-link" value="{{ __('referral.link.label') }}" />
                <div class="mt-2 flex">
                    <span class="block w-full border bg-gray-50  border-gray-300 rounded  px-4 py-1.5 ">{{ $referralUrl }}</span>
                    <button type="button"
                        x-data="{ copied: false }"
                        @click="
                            navigator.clipboard.writeText('{{ $referralUrl }}');
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        "
                        class="ml-3 p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                    >
                        <span x-show="!copied">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                            </svg>
                        </span>
                        <span x-show="copied" x-cloak>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>
