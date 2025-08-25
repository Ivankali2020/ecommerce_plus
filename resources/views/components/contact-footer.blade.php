<div x-data="{ open: false }" class="fixed bottom-4 text-green-500 right-4 z-50" style="right:20px;bottom:20px">
    <!-- Toggle Button -->
    <button @click="open = !open" class="bg-green-500 hover:bg-green-500 text-white rounded-full p-3 shadow-lg flex items-center justify-center transition-all duration-300 focus:outline-none">
        <x-heroicon-s-chat-bubble-left-right x-show="!open" class="w-6 h-6" />
        <x-heroicon-s-x-mark x-show="open" class="w-6 h-6" />
    </button>
    
    <!-- Contact Card -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 mt-3 w-64 border border-gray-200 dark:border-gray-700">
        <div class="font-bold text-primary-600 dark:text-primary-400 text-lg mb-4">{{ __('contact.title') }}</div>
        
        <div class="space-y-3 ">
            <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                <x-heroicon-s-phone class="w-5 h-5 text-primary-500 dark:text-primary-400 shrink-0" />
                <span class="text-sm">{{ __('contact.phone') }}</span>
            </div>
            
            <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                <x-heroicon-s-envelope class="w-5 h-5 text-primary-500 dark:text-primary-400 shrink-0" />
                <span class="text-sm">{{ __('contact.email') }}</span>
            </div>
            
            <div class="pt-2 text-xs text-center text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                {{ __('contact.copyright') }}
            </div>
        </div>
    </div>
</div>