@props(['for'])

@error($for)
    <p {{ $attributes->class('mt-2 text-xs text-sale') }}>{{ $message }}</p>
@enderror
