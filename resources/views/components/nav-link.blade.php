@props(['active'])

@php
$isMyanmar = app()->currentLocale() === 'my';
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#c2a03d] dark:border-[#c2a03d] text-sm font-medium leading-5 text-[#c2a03d] dark:text-[#c2a03d] focus:outline-none focus:border-[#c2a03d] transition duration-200 ease-in-out ' . ($isMyanmar ? 'font-mm' : 'font-jost')
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300 dark:hover:border-slate-700 focus:outline-none focus:text-slate-700 dark:focus:text-slate-300 focus:border-slate-300 dark:focus:border-slate-700 transition duration-150 ease-in-out ' . ($isMyanmar ? 'font-mm' : '');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
