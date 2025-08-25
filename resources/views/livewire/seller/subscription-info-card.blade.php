<div class="fi-sidebar-item border-none ">
    <div class="border-none ">
        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm uppercase text-center " >
                Subscription Status
            </h3>
        </div>
        
        <div class="fi-section-content p-4 pt-3">
            @if($subscriptionData['hasSubscription'])
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Plan</span>
                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $subscriptionData['planName'] }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Status</span>
                        <span class="text-xs font-medium">
                            @if($subscriptionData['isExpired'])
                                <span style="--c-50:var(--danger-50);--c-400:var(--danger-400);--c-600:var(--danger-600);" class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-6 py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30">
                                    <span class="grid">
                                        <span class="truncate">Expired</span>
                                    </span>
                                </span>
                            @else
                                <span style="--c-50:var(--success-50);--c-400:var(--success-400);--c-600:var(--success-600);" class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-6 py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30">
                                    <span class="grid">
                                        <span class="truncate">Active</span>
                                    </span>
                                </span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Expires</span>
                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $subscriptionData['expiryDate']->format('M d, Y') }}</span>
                    </div>
                    
                    @if(!$subscriptionData['isExpired'])
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Days Left</span>
                            <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ number_format($subscriptionData['daysRemaining'],0) }} days</span>
                        </div>
                        
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full mt-1" style="height:4px">
                            @php
                                $progressPercent = min(100, max(0, $subscriptionData['daysRemaining'] / 30 * 100));
                                $progressColor = $progressPercent > 50 ? 'green' : ($progressPercent > 20 ? 'yellow' : 'danger');
                            @endphp
                            {{-- <div  class=rounded-full" style="height:4px;width: {{ $progressPercent  }}%; background: green"></div> --}}
                            <div class="rounded-full fi-color-custom {{ $progressColor }}" 
                                 style=" height:4px;
                                        width: {{ $progressPercent }}%; 
                                        background-color: {{ $progressColor }};
                                        dark:background-color: {{ $progressColor }};">
                            </div>
                        </div>
                       
                    @endif
                    
                    @if($subscriptionData['isExpired'])
                    <a href="{{ route('seller.subscription.plans') }}" class="fi-link fi-link-size-sm inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus:ring-primary-500 dark:focus:ring-offset-gray-900">
                        Renew Subscription
                    </a>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No active subscription found.</p>
                <a href="{{ route('seller.subscription.plans') }}" class="fi-link fi-link-size-sm mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus:ring-primary-500 dark:focus:ring-offset-gray-900">
                    Get Subscription
                </a>
            @endif
        </div>
    </div>
</div>