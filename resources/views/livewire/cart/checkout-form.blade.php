<div class="space-y-4">
    {{-- Note --}}
    <div class="space-y-2">
        <label for="note" class="block text-sm font-medium text-gray-700 ">
            {{ __('messages.cart.note') }}
        </label>
       
        <x-textarea id="note" wire:model="note" rows="3"
            class="mt-1 block w-full rounded-md"
            placeholder="{{ __('messages.cart.note') }}"></x-textarea>
        
        {{-- Validation Error --}}
        @error('note')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Checkout Button --}}
    <button wire:click="checkout" wire:loading.attr="disabled"
        class="w-full bg-slate-900 text-white py-3 rounded-lg hover:bg-slate-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed ">
        <span wire:loading.remove>{{ __('messages.cart.proceed_checkout') }}</span>
        <span wire:loading>{{ __('messages.cart.processing') }}...</span>
    </button>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 ">
                        {{ __('messages.cart.validation_errors') }}
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
