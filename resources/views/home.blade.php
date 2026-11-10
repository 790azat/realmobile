<x-layouts.shop>
    {{-- Герой в стиле коммерческого предложения: тёмный градиент с красным --}}
    <section class="relative overflow-hidden bg-[radial-gradient(120%_120%_at_100%_0%,#5a1220_0%,#1d1118_45%,#141217_100%)] text-white">
        <div class="container-x grid items-center gap-8 py-10 sm:py-14 lg:grid-cols-[1.1fr_1fr] lg:py-20">
            <div>
                <div class="text-xs font-semibold uppercase tracking-[.2em] text-brand">Новинка · {{ $flagship?->name }}</div>
                <h1 class="mt-4 text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl lg:text-6xl">
                    Оригинальные <span class="text-brand">iPhone</span> в наличии
                </h1>
                <p class="mt-5 max-w-lg text-lg leading-relaxed text-neutral-300">
                    От iPhone 14 до 18 Pro Max. Все цвета и объёмы памяти, честная цена и наличие на сайте. Оставьте заявку или напишите менеджеру.
                </p>
                <ul class="mt-6 space-y-2 text-[15px] text-neutral-200">
                    @foreach (['Оригинал, гарантия 12 месяцев', 'Проверка при покупке', 'Ответ в Telegram и WhatsApp за 15 минут'] as $li)
                        <li class="flex items-center gap-2"><svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>{{ $li }}</li>
                    @endforeach
                </ul>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#catalog" class="btn-brand px-6 py-3.5 text-base">Выбрать iPhone</a>
                    <a href="{{ tg_link('Здравствуйте! Помогите подобрать iPhone.') }}" target="_blank" rel="noopener" class="btn border border-white/20 px-6 py-3.5 text-base text-white hover:bg-white/10">@include('partials.icon-tg', ['class' => 'h-5 w-5']) Подобрать с менеджером</a>
                </div>
            </div>
            @if ($flagship)
                <a href="{{ route('product', $flagship->slug) }}" class="relative mx-auto block w-full max-w-md">
                    <div class="absolute inset-8 rounded-full bg-brand/25 blur-3xl"></div>
                    <img src="{{ $flagship->mainImage() }}" alt="{{ $flagship->name }}" fetchpriority="high" width="900" height="900" class="relative w-full drop-shadow-2xl">
                    <div class="absolute bottom-4 left-4 rounded-2xl bg-white/95 px-4 py-3 text-ink shadow-xl">
                        <div class="text-sm font-semibold">{{ $flagship->name }}</div>
                        <div class="text-lg font-extrabold text-brand">от {{ money($flagship->minPrice()) }}</div>
                    </div>
                </a>
            @endif
        </div>
    </section>

    {{-- Быстрый выбор модели --}}
    <section class="border-b border-line bg-white">
        <div class="container-x flex gap-3 overflow-x-auto py-5 [scrollbar-width:none]">
            @foreach ($phones as $p)
                <a href="{{ route('product', $p->slug) }}" class="group flex w-24 shrink-0 flex-col items-center text-center sm:w-28">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl bg-paper transition group-hover:bg-brand-soft sm:h-24 sm:w-24">
                        <img src="{{ $p->mainImage() }}" alt="{{ $p->name }}" loading="lazy" class="h-full w-full object-contain p-1.5">
                    </div>
                    <div class="mt-2 text-xs font-medium leading-tight sm:text-sm">{{ str_replace('iPhone ', '', $p->name) }}</div>
                </a>
            @endforeach
        </div>
    </section>

    <section id="catalog" class="container-x scroll-mt-20 py-10">
        <livewire:catalog :embedded="true" />
    </section>

    {{-- Почему мы --}}
    <section class="container-x py-6">
        <div class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['Наличие на виду', 'По каждому цвету и объёму памяти видно, сколько осталось. Не нужно звонить и уточнять.'],
                ['Только оригинал', 'Новые запечатанные iPhone с гарантией 12 месяцев. Проверяем при вас.'],
                ['Быстрый ответ', 'Заявка сразу попадает менеджеру в Telegram. Перезвоним в течение 15 минут.'],
            ] as [$t, $d])
                <div class="border-t-4 border-brand pt-4">
                    <div class="text-lg font-bold">{{ $t }}</div>
                    <p class="mt-1 text-mute">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Аксессуары --}}
    <section class="container-x py-10">
        <div class="mb-5 flex items-end justify-between">
            <h2 class="text-2xl font-bold sm:text-3xl">Аксессуары</h2>
            <a href="{{ route('accessories') }}" class="text-sm font-medium text-brand hover:underline">Все аксессуары →</a>
        </div>
        @include('partials.accessory-cats', ['cats' => $accCats])
    </section>

    {{-- CTA --}}
    <section class="container-x pb-4 pt-6">
        <div class="flex flex-col items-start gap-5 rounded-3xl bg-brand-soft p-6 sm:p-10 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="text-2xl font-bold">Не нашли нужную модель?</div>
                <p class="mt-1 text-mute">Напишите менеджеру: подберём цвет и память или привезём под заказ.</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                <button type="button" x-data x-on:click="$dispatch('open-lead', { product: '' })" class="btn-brand px-6">Оставить заявку</button>
                <a href="{{ tg_link('Здравствуйте! Ищу iPhone, помогите подобрать.') }}" target="_blank" rel="noopener" class="btn-tg px-6">@include('partials.icon-tg', ['class' => 'h-4 w-4']) Telegram</a>
                <a href="{{ wa_link('Здравствуйте! Ищу iPhone, помогите подобрать.') }}" target="_blank" rel="noopener" class="btn-wa px-6">@include('partials.icon-wa', ['class' => 'h-4 w-4']) WhatsApp</a>
            </div>
        </div>
    </section>
</x-layouts.shop>
