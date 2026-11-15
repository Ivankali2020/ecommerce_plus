@props(['active' => false])

<a {{ $attributes->class([
    'relative inline-flex h-full items-center text-[13px] tracking-wide transition-colors duration-200',
    'after:absolute after:inset-x-0 after:bottom-0 after:h-px after:origin-left after:bg-ink after:transition-transform after:duration-300',
    'text-ink after:scale-x-100' => $active,
    'text-ink-soft hover:text-ink after:scale-x-0 hover:after:scale-x-100' => ! $active,
]) }}>{{ $slot }}</a>
