<div class="sticky top-0 z-20">
<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use App\Helper\UserHelper;
use App\Models\Tenant;

new class extends Component
{
    public $seller;
    public $logo;


    public function mount()
    {
        $this->seller = Tenant::where('id',tenant('id'))->first()->sellers()->first();
        $this->logo = $this->seller->getFirstMediaUrl('logos');
    }
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function sellerId()
    {
        return UserHelper::getUserSellerId();
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white/10 dark:bg-slate-800/80 backdrop-filter backdrop-blur-md border-b border-slate-100 dark:border-slate-700">
    <!-- Primary Navigation Menu -->
    <div class="container  mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center ">
                <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('welcome') }}" >
                            @if ($this->logo)
                                <img src="{{ $this->logo != null ? $this->logo : asset('assets/logo.png') }}" alt="{{  $seller->name }}" class="block rounded h-12 w-auto fill-current text-slate-800 dark:text-slate-200" />
                                @else
                                <h1 class="text-2xl font-bold font-bebas text-center ">{{  $seller->name  }}</h1>
                            @endif
                        </a>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-[100%] ">
                        <x-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')" wire:navigate>
                            {{ __('messages.navigation.main_store') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-[100%] ">
                        <x-nav-link :href="route('products')" :active="request()->routeIs('products')" wire:navigate>
                            {{ __('messages.navigation.products') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-[100%] ">
                        <x-nav-link :href="route('wholesale')" :active="request()->routeIs('wholesale')" wire:navigate>
                            {{ __('messages.navigation.wholesale') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-[100%] ">
                        <x-nav-link :href="route('delivery')" :active="request()->routeIs('delivery')" wire:navigate>
                            {{ __('messages.navigation.delivery') }}
                        </x-nav-link>
                    </div>

                @auth

                    <!-- Navigation Links -->
                    {{-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-[100%] ">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" >
                            {{ __('messages.navigation.dashboard') }}
                        </x-nav-link>
                    </div> --}}


                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-[100%] ">
                        <x-nav-link :href="route('order')" :active="request()->routeIs('order')" wire:navigate>
                            {{ __('messages.navigation.order') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-[100%] ">
                        <x-nav-link :href="route('recent.orders')" :active="request()->routeIs('recent.orders')" wire:navigate>
                            {{ __('messages.navigation.recent_orders') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-[100%] ">
                        <x-nav-link :href="route('gifts')" :active="request()->routeIs('gifts')" wire:navigate>
                            {{ __('gifts.gifts') }}
                        </x-nav-link>
                    </div>

                    
                @endauth

            </div>

            @auth

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 ">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger" >
                        <button class="{{ app()->currentLocale() === 'my' ? 'font-mm' : '' }} hover:bg-gray-50 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate class="flex gap-3 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                              </svg>
                            {{ __('messages.auth.profile.title') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('dashboard')" wire:navigate class="flex gap-3 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 " fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m7.875 14.25 1.214 1.942a2.25 2.25 0 0 0 1.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 0 1 1.872 1.002l.164.246a2.25 2.25 0 0 0 1.872 1.002h2.092a2.25 2.25 0 0 0 1.872-1.002l.164-.246A2.25 2.25 0 0 1 16.954 9h4.636M2.41 9a2.25 2.25 0 0 0-.16.832V12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 12V9.832c0-.287-.055-.57-.16-.832M2.41 9a2.25 2.25 0 0 1 .382-.632l3.285-3.832a2.25 2.25 0 0 1 1.708-.786h8.43c.657 0 1.281.287 1.709.786l3.284 3.832c.163.19.291.404.382.632M4.5 20.25h15A2.25 2.25 0 0 0 21.75 18v-2.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            {{ __('messages.navigation.dashboard') }}
                        </x-dropdown-link>



                        <x-dropdown-link :href="route('recent.orders')" wire:abort="navigate" class="flex gap-3 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            {{ __('messages.navigation.recent_orders') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('gifts')" wire:abort="navigate" class="flex gap-3 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                            </svg>

                            {{ __('gifts.gifts') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start bg-red-100 rounded-sm ">
                            <x-dropdown-link class="flex gap-3 items-center text-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                                  </svg>
                                {{ __('messages.auth.logout') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>   


                <!-- Add this right before the Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center ">
                    <x-language-switcher />
                </div>

                <livewire:cart-nav-item />
            </div>
            @else

            <div class="flex ">
                <div class="hidden space-x-4 sm:-my-px sm:ms-4 sm:flex">
                    <x-nav-link :href="route('login')" :active="request()->routeIs('login')" wire:navigate>
                        {{ __('messages.auth.login') }}
                    </x-nav-link>
                </div>

                <div class="hidden space-x-4 sm:-my-px sm:ms-4 sm:flex">
                    <x-nav-link :href="route('register')" :active="request()->routeIs('register')" wire:navigate>
                        {{ __('messages.auth.register') }}
                    </x-nav-link>
                </div>
            </div>

            @endauth


            <!-- Hamburger -->  
            <div class="-me-2 flex items-center sm:hidden  {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">


                @guest
                    <div class=" space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')" >
                            {{ __('messages.auth.login') }}
                        </x-responsive-nav-link>
                    </div>
                    @else
                    {{-- <a href="{{ route('dashboard') }}" class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 {{ request()->routeIs('dashboard') ? 'text-[#c2a03d]' : 'text-gray-500' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                          </svg>
                    </a> --}}
                @endguest
                

                <!-- Add this right before the Settings Dropdown -->
                <div class="lg:hidden sm:flex sm:items-center ">
                    <x-language-switcher />
                </div>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    {{-- <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg> --}}
                      <svg :class="{'hidden': open, 'inline-flex': ! open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                      </svg>
                      
                      <svg :class="{'hidden': ! open, 'inline-flex': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                      </svg>
                      
                </button>

            </div>
        </div>
    </div>


    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600  {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
            @auth
            <div class="px-4 ">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">{{ auth()->user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile',Auth::user()->id)" wire:navigate>
                    {{ __('messages.auth.profile.title') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('welcome')" wire:navigate>
                    {{ __('messages.navigation.main_store') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('delivery')" wire:navigate>
                    {{ __('messages.navigation.delivery') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('dashboard')" wire:navigate>
                    {{ __('messages.navigation.dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('cart')" wire:navigate>
                    {{ __('messages.navigation.cart') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('order')" wire:navigate>
                    {{ __('messages.navigation.order') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('recent.orders')" wire:navigate>
                    {{ __('messages.navigation.recent_orders') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('gifts')" wire:navigate>
                   {{ __('gifts.gifts') }}
                </x-responsive-nav-link>


                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('messages.auth.logout') }}
                    </x-responsive-nav-link>
                </button>
            </div>
            @else

            <div class=" space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')" >
                    {{ __('messages.auth.login') }}
                </x-responsive-nav-link>
            </div>
            <div class=" space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')" >
                    {{ __('messages.auth.register') }}
                </x-responsive-nav-link>
            </div>

            @endauth

            
           
        </div>
    </div>
</nav>

<!-- Mobile Bottom Navigation -->
@auth
<div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-t-gray-200 sm:hidden {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
    <div class=" h-full flex justify-around items-center ">
        <a wire:navigate href="{{ route('welcome') }}"  class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 {{ request()->routeIs(['store', 'store.*']) ? 'text-[#c2a03d]' : 'text-gray-500' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 mb-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>
            <span class="text-xs">{{ __('messages.store') }}</span>
        </a>
        
        <a wire:navigate href="{{ route('products') }}" class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 {{ request()->routeIs('products') ? 'text-[#c2a03d]' : 'text-gray-500' }}">
           
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 mb-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
              </svg>
              
            <span class="text-xs">{{ __('messages.navigation.products') }}</span>
        </a>
        
        <a wire:navigate href="{{ route('wholesale') }}" class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 {{ request()->routeIs('wholesale') ? 'text-[#c2a03d]' : 'text-gray-500' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 w-5 h-5 mb-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.016A3.001 3.001 0 0 0 20.25 8.734V21M3.75 9.349v-2.136A3.001 3.001 0 0 0 6.75 4.5h10.5A3.001 3.001 0 0 0 20.25 7.213v2.136" />
            </svg>
            <span class="text-xs">{{ __('messages.navigation.wholesale') }}</span>
        </a>
        
        <!-- Replace the mobile cart link with the Livewire component -->
        <livewire:cart-nav-item />
        
        <a wire:navigate href="{{ route('order') }}" class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 {{ request()->routeIs('order') ? 'text-[#c2a03d]' : 'text-gray-500' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 mb-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
              </svg>
            <span class="text-xs">{{ __('messages.navigation.order') }}</span>
        </a>
        
        <a wire:navigate href="{{ route('profile') }}" class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 {{ request()->routeIs('profile') ? 'text-[#c2a03d]' : 'text-gray-500' }}">
        
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 mb-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>
              
            <span class="text-xs">{{ __('messages.auth.profile.title') }}</span>
        </a>
    </div>
</div>
@endauth
</div>
