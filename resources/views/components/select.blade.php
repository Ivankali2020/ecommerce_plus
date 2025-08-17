@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-slate-500 dark:focus:border-slate-600 focus:outline-none focus:ring-0 rounded-md']) }}>
    {{ $slot }}
</select>
