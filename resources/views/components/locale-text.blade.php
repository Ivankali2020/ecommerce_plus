<div {{ $attributes->merge(['class' => $isMyanmar() ? 'font-mm text-3xl ' : 'font-bebas text-2xl ']) }}>
    {{ $slot }}
</div>
