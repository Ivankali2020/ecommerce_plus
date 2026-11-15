@props(['disabled' => false])

<textarea @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink placeholder:text-muted transition-colors focus:border-ink/50 focus:outline-none focus:ring-0']) }}>{{ $slot }}</textarea>
