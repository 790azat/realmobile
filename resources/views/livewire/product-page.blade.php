@php
    $p = $product;
    $storages = $p->storages();
    $colors = $p->colors();
    [$stockText, $stockClass] = stock_label((int) ($variant?->stock ?? 0));
    $backUrl = $p->isPhone() ? route('catalog') : ($p->category ? route('accessories.category', $p->category->slug) : route('accessories'));
@endphp
@push('head')
    <script type="application/ld+json">{!! json_encode([
        '@'.'context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $p->name,
        'brand' => ['@type' => 'Brand', 'name' => 'Apple'],
        'image' => url($p->mainImage()),
        'description' => $p->description,
        'offers' => [
            '@type' => 'AggregateOffer',
            'priceCurrency' => 'RUB',
            'lowPrice' => $p->variants->min('price'),
            'highPrice' => $p->variants->max('price'),
            'offerCount' => $p->variants->count(),
            'availability' => $p->totalStock() > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

<div class="container-x py-6 pb-32 lg:pb-10">
    <nav class="mb-4 text-sm text-mute">
        <a href="{{ route('home') }}" class="hover:text-ink">Главная</a> /
        <a href="{{ $backUrl }}" class="hover:text-ink">{{ $p->isPhone() ? 'iPhone' : ($p->category?->name ?? 'Аксессуары') }}</a> /
        <span class="text-ink">{{ $p->name }}</span>
    </nav>

    <div class="grid gap-8 lg:grid-cols-2 lg:gap-12">
        {{-- Фото --}}
        <div>
            <div class="card relative flex aspect-[5/4] items-center justify-center overflow-hidden bg-paper sm:aspect-square">
                @if ($p->badge)<span class="badge absolute left-4 top-4 z-10">{{ $p->badge }}</span>@endif
                <img src="{{ $image }}" alt="{{ $label }}" wire:key="img-{{ md5($image) }}" class="h-full w-full object-contain p-6" fetchpriority="high" width="900" height="900">
            </div>
            @if ($colors->count() > 1)
                <div class="mt-3 grid grid-cols-5 gap-2 sm:grid-cols-6">
                    @foreach ($colors as $c)
                        <button type="button" wire:click="selectColor(@js($c->color))" @class(['card aspect-square overflow-hidden bg-paper p-1.5 transition', 'ring-2 ring-brand border-brand' => $c->color === $color]) title="{{ $c->color }}">
                            <img src="{{ $c->image ?: $p->mainImage() }}" alt="{{ $p->name }} {{ $c->color }}" loading="lazy" class="h-full w-full object-contain">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Покупка --}}
        <div>
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $p->isPhone() ? 'Apple ' : '' }}{{ $p->name }}</h1>
            @if ($p->description)
                <p class="mt-3 leading-relaxed text-mute">{{ $p->description }}</p>
            @endif

            <div class="mt-5 flex flex-wrap items-baseline gap-3">
                @if ($variant)
                    <span class="text-4xl font-extrabold tracking-tight">{{ money($variant->price) }}</span>
                    @if ($variant->old_price)
                        <span class="text-lg text-mute line-through">{{ money($variant->old_price) }}</span>
                        <span class="badge">−{{ $variant->discountPercent() }}%</span>
                    @endif
                @endif
            </div>

            @if ($storages->count())
                <div class="mt-6">
                    <div class="mb-2 text-sm text-mute">Память</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($storages as $s)
                            @php
                                $sv = $p->variants->where('storage', $s->storage);
                                $sPrice = ($sv->firstWhere('color', $color) ?? $sv->sortBy('price')->first())->price;
                                $sStock = $sv->sum('stock');
                            @endphp
                            <button type="button" wire:click="selectStorage(@js($s->storage))"
                                @class(['chip px-4 py-2.5', 'chip-on font-semibold' => $s->storage === $storage, 'opacity-50' => ! $sStock])>
                                {{ $s->storage }} <span class="text-xs {{ $s->storage === $storage ? '' : 'text-mute' }}">· {{ number_format($sPrice, 0, '', ' ') }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($colors->count())
                <div class="mt-5">
                    <div class="mb-2 text-sm text-mute">Цвет: <span class="font-medium text-ink">{{ $color }}</span></div>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach ($colors as $c)
                            @php $cStock = $p->variants->where('color', $c->color)->where('storage', $storage)->sum('stock'); @endphp
                            <button type="button" wire:click="selectColor(@js($c->color))" title="{{ $c->color }}"
                                @class(['relative h-10 w-10 rounded-full ring-1 ring-black/10 transition', 'outline-2 outline-offset-2 outline-brand' => $c->color === $color])
                                style="background: {{ $c->color_hex ?: '#ddd' }}">
                                @unless ($cStock)
                                    <span class="absolute inset-0 m-auto h-[2px] w-9 -rotate-45 bg-neutral-500/60"></span>
                                @endunless
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-5 text-sm font-medium {{ $stockClass }}">● {{ $stockText }}@if (($variant?->stock ?? 0) > 0) · можно забрать сегодня @else · привезём под заказ @endif</div>

            {{-- Три кнопки --}}
            <div id="buy-buttons" class="mt-6 grid gap-2.5 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3">
                <button type="button" class="btn-brand py-3.5"
                    x-on:click="$dispatch('open-lead', { product: @js($label), productId: {{ $p->id }}, variantId: {{ $variant?->id ?? 'null' }} })">
                    Оставить заявку
                </button>
                <a href="{{ tg_link($message) }}" target="_blank" rel="noopener" class="btn-tg py-3.5"
                   x-on:click="copyText(@js($message)).then(ok => ok && $dispatch('toast', { message: 'Текст про {{ $p->name }} скопирован: вставьте его в чат' }))">
                    @include('partials.icon-tg', ['class' => 'h-5 w-5']) Telegram
                </a>
                <a href="{{ wa_link($message) }}" target="_blank" rel="noopener" class="btn-wa py-3.5">
                    @include('partials.icon-wa', ['class' => 'h-5 w-5']) WhatsApp
                </a>
            </div>
            <p class="mt-2 text-xs text-mute">Менеджер ответит в течение 15 минут и подтвердит наличие выбранного варианта.</p>

            @if ($p->specs)
                <div class="mt-8">
                    <h2 class="mb-3 text-lg font-bold">Характеристики</h2>
                    <dl class="divide-y divide-line rounded-2xl border border-line">
                        @foreach ($p->specs as $k => $v)
                            <div class="grid grid-cols-[40%_1fr] gap-3 px-4 py-3 text-sm">
                                <dt class="text-mute">{{ $k }}</dt>
                                <dd class="font-medium">{{ $v }}</dd>
                            </div>
                        @endforeach
                        @if ($storages->count())
                            <div class="grid grid-cols-[40%_1fr] gap-3 px-4 py-3 text-sm">
                                <dt class="text-mute">Память</dt>
                                <dd class="font-medium">{{ $storages->pluck('storage')->implode(', ') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>
    </div>

    @if ($similar->count())
        <section class="mt-14">
            <h2 class="mb-4 text-2xl font-bold">Похожие модели</h2>
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach ($similar as $s)
                    @include('partials.product-card', ['product' => $s, 'variants' => $s->variants])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Липкая панель на телефоне --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-white/95 p-3 backdrop-blur lg:hidden" style="padding-bottom: max(.75rem, env(safe-area-inset-bottom))">
        <div class="mb-2 flex items-baseline justify-between px-1">
            <span class="truncate text-sm text-mute">{{ $variant?->label() }}</span>
            <span class="text-lg font-bold">{{ $variant ? money($variant->price) : '' }}</span>
        </div>
        <div class="grid grid-cols-[1fr_auto_auto] gap-2">
            <button type="button" class="btn-brand"
                x-on:click="$dispatch('open-lead', { product: @js($label), productId: {{ $p->id }}, variantId: {{ $variant?->id ?? 'null' }} })">Оставить заявку</button>
            <a href="{{ tg_link($message) }}" target="_blank" rel="noopener" class="btn-tg px-3.5" aria-label="Telegram"
               x-on:click="copyText(@js($message))">@include('partials.icon-tg', ['class' => 'h-5 w-5'])</a>
            <a href="{{ wa_link($message) }}" target="_blank" rel="noopener" class="btn-wa px-3.5" aria-label="WhatsApp">@include('partials.icon-wa', ['class' => 'h-5 w-5'])</a>
        </div>
    </div>
</div>
