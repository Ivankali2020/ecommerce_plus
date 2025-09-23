<div class="text-center py-12">
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-content p-8">
            <svg class="mx-auto h-24 w-24 text-danger-500 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            
            <h3 class="mt-4 text-lg font-medium text-danger-600 dark:text-danger-400">Preview Error</h3>
            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">An error occurred while generating the preview:</p>
            
            <div class="mt-4 fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-content p-4">
                    <code class="text-xs text-danger-600 dark:text-danger-400 break-all">{{ $error }}</code>
                </div>
            </div>
            
            <div class="mt-6">
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="fi-section-content p-4">
                        <h4 class="text-sm font-medium text-primary-600 dark:text-primary-400 mb-2">Current Filters:</h4>
                        <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                            <div><strong>Active Tab:</strong> {{ ucfirst($activeTab) }}</div>
                            @if(!empty($filters['created_at']['From']) || !empty($filters['created_at']['To']))
                            <div>
                                <strong>Date Range:</strong>
                                {{ !empty($filters['created_at']['From']) ? \Carbon\Carbon::parse($filters['created_at']['From'])->format('M d, Y') : 'Start' }}
                                -
                                {{ !empty($filters['created_at']['To']) ? \Carbon\Carbon::parse($filters['created_at']['To'])->format('M d, Y') : 'End' }}
                            </div>
                            @endif
                            @if(!empty($filters['is_cod']['cod']))
                            <div><strong>COD Filters:</strong> {{ implode(', ', $filters['is_cod']['cod']) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-sm text-danger-600 dark:text-danger-400">
                <p>Please try refreshing the page or contact support if the issue persists.</p>
            </div>
        </div>
    </div>
</div>
