<div class="text-center py-12">
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-content p-8">
            <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            
            <h3 class="mt-4 text-lg font-medium text-gray-950 dark:text-white">No Data Found</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $message }}</p>
            
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
            
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                <p>Try adjusting your filters or selecting a different tab to see data.</p>
            </div>
            
            @if(isset($debug))
            <div class="mt-6">
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="fi-section-content p-4">
                        <h4 class="text-sm font-medium text-gray-950 dark:text-white mb-2">Debug Information:</h4>
                        <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                            <div><strong>Total Records Found:</strong> {{ $debug['total_records'] }}</div>
                            <div><strong>Active Tab:</strong> {{ $debug['active_tab'] }}</div>
                            <div><strong>Has Filters:</strong> {{ $debug['has_filters'] ? 'Yes' : 'No' }}</div>
                            @if($debug['first_record'])
                            <div><strong>First Record:</strong></div>
                            <div class="ml-4">
                                <div>ID: {{ $debug['first_record']['id'] }}</div>
                                <div>Product Name: {{ $debug['first_record']['product_name'] ?? 'NULL' }}</div>
                                <div>Has Product: {{ $debug['first_record']['has_product'] ? 'Yes' : 'No' }}</div>
                                <div>Has Order: {{ $debug['first_record']['has_order'] ? 'Yes' : 'No' }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
