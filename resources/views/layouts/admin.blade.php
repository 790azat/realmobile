@php
    $newLeads = \App\Models\Lead::where('status', 'new')->count();
    $nav = [
        ['admin.dashboard', 'Обзор', null],
        ['admin.leads', 'Заявки', $newLeads],
        ['admin.products', 'Товары', null],
        ['admin.categories', 'Категории', null],
    ];
@endphp
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title ?? 'Админ-панель' }} · {{ config('shop.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700;800&display=swap&subset=cyrillic" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-paper" x-data="{ menu: false }">
    <div class="flex min-h-screen">
        <aside :class="menu ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 w-60 bg-ink text-neutral-300 transition lg:static lg:translate-x-0">
            <div class="px-5 py-5">
                @include('partials.logo', ['dark' => true])
                <div class="mt-1 text-xs text-brand">Админ-панель</div>
            </div>
            <nav class="mt-2 space-y-0.5">
                @foreach ($nav as [$route, $label, $count])
                    <a href="{{ route($route) }}" wire:navigate @class(['flex items-center justify-between px-5 py-2.5 text-[15px] hover:bg-white/5', 'bg-brand text-white hover:bg-brand' => request()->routeIs($route.'*')])>
                        {{ $label }}
                        @if ($count)<span class="rounded-full bg-brand px-2 text-xs text-white">{{ $count }}</span>@endif
                    </a>
                @endforeach
            </nav>
            <div class="absolute inset-x-0 bottom-0 space-y-1 p-5 text-sm">
                <a href="{{ route('home') }}" target="_blank" class="block hover:text-white">Открыть сайт ↗</a>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="hover:text-white">Выйти</button></form>
            </div>
        </aside>
        <div x-show="menu" x-cloak x-on:click="menu = false" class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

        <div class="min-w-0 flex-1">
            <header class="flex h-14 items-center gap-3 border-b border-line bg-white px-4 lg:hidden">
                <button x-on:click="menu = true" class="rounded-lg p-1.5" aria-label="Меню"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
                <span class="font-semibold">{{ $title ?? 'Админ-панель' }}</span>
            </header>
            <main class="p-4 sm:p-6 lg:p-8">{{ $slot }}</main>
        </div>
    </div>
    <div x-data="{ show: false, msg: '' }" x-on:toast.window="msg = $event.detail.message; show = true; setTimeout(() => show = false, 2500)"
         x-show="show" x-transition x-cloak class="fixed bottom-6 right-6 z-[70] rounded-xl bg-ink px-4 py-3 text-sm text-white shadow-xl" x-text="msg"></div>
    @livewireScripts
</body>
</html>
