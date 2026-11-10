@php
    $shop = config('shop.name');
    $title = $title ?? trim("{$shop} — оригинальные iPhone в наличии ".config('shop.city_in'));
    $description = $description ?? 'Оригинальные iPhone 18 Pro Max, 18 Pro, 17, Air, 17e, 16, 15, 14 в наличии. Все цвета и объёмы памяти. Оставьте заявку или напишите менеджеру в Telegram и WhatsApp.';
    $canonical = $canonical ?? url()->current();
    $ogImage = $ogImage ?? url('/img/iphone-18-pro-max');
@endphp
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $shop }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="ru_RU">
    <meta name="twitter:card" content="summary_large_image">
    @if (config('app.env') !== 'production' || request()->getHost() !== parse_url(config('app.url'), PHP_URL_HOST))
        {{-- Демо-домен не индексируем, чтобы не мешать будущему боевому домену --}}
        <meta name="robots" content="noindex">
    @endif
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700;800&display=swap&subset=cyrillic" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Store',
        'name' => $shop,
        'url' => url('/'),
        'telephone' => '+'.config('shop.telegram'),
        'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'RU'],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @stack('head')
</head>
<body class="min-h-screen flex flex-col">
    @include('partials.header')

    <main class="flex-1">
        {{ $slot }}
    </main>

    @include('partials.footer')

    <livewire:lead-form />

    {{-- Тост «скопировано» --}}
    <div x-data="{ show: false, msg: '' }"
         x-on:toast.window="msg = $event.detail.message; show = true; setTimeout(() => show = false, 3200)"
         x-show="show" x-transition x-cloak
         class="fixed inset-x-0 bottom-24 z-[70] mx-auto w-fit max-w-[90vw] rounded-xl bg-ink px-4 py-3 text-sm text-white shadow-xl"
         x-text="msg"></div>

    @livewireScripts
</body>
</html>
