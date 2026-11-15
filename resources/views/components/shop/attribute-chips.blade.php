{{--
    Renders product/variant attribute values, e.g. [['attribute' => 'Color', 'value' => ['Red', 'Blue']]].
--}}
@props([
    'items' => [],
    'withLabels' => true,
])

@if (! empty($items))
    <div {{ $attributes->class('flex flex-wrap gap-1.5') }}>
        @foreach ($items as $item)
            <span class="inline-flex items-center rounded-full border border-line bg-canvas px-2.5 py-0.5 text-xs text-ink-soft">
                @if ($withLabels && ! empty($item['attribute']))
                    <span class="mr-1 text-muted">{{ $item['attribute'] }}:</span>
                @endif
                {{ implode(', ', (array) ($item['value'] ?? [])) }}
            </span>
        @endforeach
    </div>
@endif
