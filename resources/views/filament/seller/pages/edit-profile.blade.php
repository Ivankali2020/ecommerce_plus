

<div>
    <h1 class="fi-header-heading pb-6  text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
        Profile
    </h1>
    <x-filament-panels::form wire:submit="update">
        {{ $this->form }}
        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </x-filament-panels::form>
</div>
