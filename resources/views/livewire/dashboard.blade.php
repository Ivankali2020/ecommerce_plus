<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-700">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-slate-500 bg-opacity-10">
                        <svg class="h-8 w-8 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm dark:text-gray-300 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                            {{ __('messages.total_orders') }}
                        </h2>
                        <p class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ $totalOrders ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <!-- Similar cards for Revenue, Pending Orders, Completed Orders -->
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.recent_orders') }}
                    </h3>
                    <div class="mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.store') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.amount') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.status') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.invoice') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-700">
                                @foreach($recentOrders ?? [] as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $order->seller->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $order->total_price }} Ks
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <p class="inline text-end w-full uppercase px-3 py-1 rounded-full text-xs font-medium {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}
                                        @switch($order->status)
                                            @case(\App\Helper\OrderStatus::New)
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @case(\App\Helper\OrderStatus::Processing)
                                                bg-blue-100 text-blue-800
                                                @break
                                            @case(\App\Helper\OrderStatus::Delivered)
                                                bg-green-100 text-green-800
                                                @break
                                            @case(\App\Helper\OrderStatus::Cancelled)
                                                bg-red-100 text-red-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ __('messages.order_status.' . strtolower($order->status->value)) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('order.detail', $order) }}" class="text-sm text-slate-600 hover:text-slate-700 dark:text-slate-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                            {{ __('messages.view_details') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    {{ __('messages.order_analytics') }}
                </h3>
                <div class="mt-4" style="height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const title = '{{ __("messages.sales") }}';
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? []) !!},
            datasets: [{
                label: title,
                data: {!! json_encode($chartData ?? []) !!},
                borderColor: '#c2a03d',
                tension: 0.1
            }]
        }
    });
});
</script>
