<div x-data="{ filters: false }" class="{{ $embedded ? '' : 'container-x py-8' }}">
    @unless ($embedded)
        <nav class="mb-3 text-sm text-mute"><a href="{{ route('home') }}" class="hover:text-ink">Главная</a> / iPhone</nav>
        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">Каталог iPhone</h1>
        <p class="mt-2 text-mute">Оригинал, все цвета и объёмы памяти. Цена и наличие обновляются менеджером ежедневно.</p>
    @endunless

    <div class="mt-6 grid gap-8 lg:grid-cols-[260px_1fr]">
        {{-- Фильтры: сайдбар на десктопе, шторка на телефоне --}}
        <aside :class="filters ? 'fixed inset-0 z-50 overflow-y-auto bg-white p-5 pb-28' : 'hidden'" class="lg:static lg:block lg:p-0">
            <div class="mb-5 flex items-center justify-between lg:hidden">
                <div class="text-xl font-bold">Фильтры</div>
                <button type="button" x-on:click="filters = false" class="rounded-lg p-2" aria-label="Закрыть">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>

            <div class="space-y-7">
                <div>
                    <div class="mb-3 text-sm font-semibold">Модель</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($all as $p)
                            <button type="button" wire:click="toggle('models', '{{ $p->slug }}')" @class(['chip', 'chip-on' => in_array($p->slug, $models)])>{{ $p->name }}</button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="mb-3 text-sm font-semibold">Память</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($storageOptions as $s)
                            <button type="button" wire:click="toggle('storages', '{{ $s }}')" @class(['chip', 'chip-on' => in_array($s, $storages)])>{{ $s }}</button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="mb-3 text-sm font-semibold">Цена, {{ config('shop.currency') }}</div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" inputmode="numeric" wire:model.live.debounce.500ms="priceFrom" class="input py-2.5" placeholder="от {{ number_format($bounds[0], 0, '', ' ') }}">
                        <input type="number" inputmode="numeric" wire:model.live.debounce.500ms="priceTo" class="input py-2.5" placeholder="до {{ number_format($bounds[1], 0, '', ' ') }}">
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                        @foreach ([[null, 60000, 'до 60 тыс.'], [60000, 100000, '60–100 тыс.'], [100000, null, 'от 100 тыс.']] as [$f, $t, $l])
                            <button type="button" wire:click="$set('priceFrom', {{ $f ?? 'null' }}); $set('priceTo', {{ $t ?? 'null' }})" @class(['chip px-2.5 py-1 text-xs', 'chip-on' => $priceFrom === $f && $priceTo === $t])>{{ $l }}</button>
                        @endforeach
                    </div>
                </div>

                <label class="flex cursor-pointer items-center gap-3 text-sm">
                    <input type="checkbox" wire:model.live="inStock" class="h-5 w-5 rounded border-line accent-[#E5243B]">
                    Только в наличии
                </label>

                @if ($activeCount)
                    <button type="button" wire:click="resetFilters" class="text-sm font-medium text-brand hover:underline">Сбросить фильтры</button>
                @endif
            </div>

            <div class="fixed inset-x-0 bottom-0 border-t border-line bg-white p-4 lg:hidden" x-show="filters">
                <button type="button" x-on:click="filters = false" class="btn-brand w-full">Показать {{ $items->count() }} {{ trans_choice('модель|модели|моделей', $items->count()) }}</button>
            </div>
        </aside>

        <section>
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="text-sm text-mute">
                    @if ($embedded)<span class="mr-1 text-lg font-bold text-ink">Все iPhone</span>@endif
                    найдено {{ $items->count() }}
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="filters = true" class="chip lg:hidden">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                        Фильтры @if ($activeCount)<span class="rounded bg-brand px-1.5 text-xs text-white">{{ $activeCount }}</span>@endif
                    </button>
                    <select wire:model.live="sort" class="rounded-lg border border-line bg-white py-1.5 pl-3 pr-8 text-sm">
                        <option value="new">Сначала новые</option>
                        <option value="cheap">Сначала дешевле</option>
                        <option value="expensive">Сначала дороже</option>
                    </select>
                </div>
            </div>

            @if ($activeCount)
                <div class="mb-4 flex flex-wrap gap-2">
                    @foreach ($models as $m)
                        <button type="button" wire:click="toggle('models', '{{ $m }}')" class="chip chip-on py-1 text-xs">{{ $all->firstWhere('slug', $m)?->name }} ✕</button>
                    @endforeach
                    @foreach ($storages as $s)
                        <button type="button" wire:click="toggle('storages', '{{ $s }}')" class="chip chip-on py-1 text-xs">{{ $s }} ✕</button>
                    @endforeach
                    @if ($priceFrom || $priceTo)
                        <button type="button" wire:click="$set('priceFrom', null); $set('priceTo', null)" class="chip chip-on py-1 text-xs">{{ $priceFrom ? number_format($priceFrom, 0, '', ' ') : '0' }}–{{ $priceTo ? number_format($priceTo, 0, '', ' ') : '∞' }} ₽ ✕</button>
                    @endif
                    @if ($inStock)
                        <button type="button" wire:click="$set('inStock', false)" class="chip chip-on py-1 text-xs">В наличии ✕</button>
                    @endif
                </div>
            @endif

            <div wire:loading.class="opacity-60" class="grid grid-cols-2 gap-3 transition sm:gap-4 xl:grid-cols-3">
                @forelse ($items as $item)
                    @include('partials.product-card', ['product' => $item['product'], 'variants' => $item['variants']])
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-line p-10 text-center">
                        <div class="text-lg font-semibold">Ничего не нашлось</div>
                        <p class="mt-1 text-mute">Попробуйте изменить фильтры или спросите менеджера: привезём нужную модель под заказ.</p>
                        <button type="button" wire:click="resetFilters" class="btn-ink mt-4">Сбросить фильтры</button>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
