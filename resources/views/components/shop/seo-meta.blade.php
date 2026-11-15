{{--
    <title>, description, Open Graph and Twitter tags. `$seo` is the optional array passed by page components.
--}}
@props([
    'seo' => [],
    'defaultTitle',
    'defaultDescription' => null,
    'defaultImage' => null,
])

@php
    $title = $seo['title'] ?? $defaultTitle;
    $description = $seo['description'] ?? $defaultDescription ?? $defaultTitle;
    $image = $seo['image'] ?? $defaultImage;
    $url = $seo['url'] ?? url()->current();
    $type = $seo['type'] ?? 'website';
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">

<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">

@if ($type === 'product')
    <meta property="product:price:amount" content="{{ $seo['price'] ?? '' }}">
    <meta property="product:price:currency" content="{{ $seo['currency'] ?? 'MMK' }}">
@endif

<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $url }}">
<meta property="twitter:title" content="{{ $title }}">
<meta property="twitter:description" content="{{ $description }}">
<meta property="twitter:image" content="{{ $image }}">
