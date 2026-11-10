@php
    $families = \App\Models\Product::where('is_active', true)->whereHas('category', fn ($q) => $q->where('slug', 'iphone'))->orderBy('sort')->get(['name', 'slug']);
    $accCats = \App\Models\Category::where('parent_id', \App\Models\Category::accessoriesRoot()?->id)->orderBy('sort')->get();
@endphp
<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-line bg-white/95 backdrop-blur">
    <div class="container-x flex h-16 items-center justify-between gap-4">
        <a href="{{ route('home') }}" aria-label="{{ config('shop.name') }} — на главную">@include('partials.logo')</a>

        <nav class="hidden items-center gap-7 text-[15px] text-ink-soft md:flex">
            <a href="{{ route('catalog') }}" class="hover:text-brand {{ request()->routeIs('catalog', 'product') ? 'text-brand font-semibold' : '' }}">iPhone</a>
            <a href="{{ route('accessories') }}" class="hover:text-brand {{ request()->routeIs('accessories*') ? 'text-brand font-semibold' : '' }}">Аксессуары</a>
            <a href="{{ route('home') }}#contacts" class="hover:text-brand">Контакты</a>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ tg_link('Здравствуйте! Хочу проконсультироваться по iPhone.') }}" target="_blank" rel="noopener"
               class="hidden items-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark sm:inline-flex">
                @include('partials.icon-tg', ['class' => 'h-4 w-4']) Написать
            </a>
            <button type="button" x-on:click="open = !open" class="rounded-lg p-2 md:hidden" aria-label="Меню">
                <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition.origin.top class="border-t border-line bg-white md:hidden">
        <div class="container-x space-y-5 py-5">
            <div>
                <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-mute">iPhone</div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($families as $f)
                        <a href="{{ route('product', $f->slug) }}" class="chip">{{ $f->name }}</a>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-mute">Аксессуары</div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($accCats as $c)
                        <a href="{{ route('accessories.category', $c->slug) }}" class="chip">{{ $c->name }}</a>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ tg_link('Здравствуйте! Хочу проконсультироваться по iPhone.') }}" target="_blank" rel="noopener" class="btn-tg">@include('partials.icon-tg', ['class' => 'h-4 w-4']) Telegram</a>
                <a href="{{ wa_link('Здравствуйте! Хочу проконсультироваться по iPhone.') }}" target="_blank" rel="noopener" class="btn-wa">@include('partials.icon-wa', ['class' => 'h-4 w-4']) WhatsApp</a>
            </div>
        </div>
    </div>
</header>
