<x-app-layout>

    <div class="pt-10 pb-20  lg:py-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 space-y-6">
            
            <livewire:points-widget>

            <livewire:referral-code-widget />

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg  lg:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg  lg:rounded-lg">
                    <livewire:profile.user-address-form />
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg  lg:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg  lg:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
