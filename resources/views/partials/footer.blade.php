<footer id="contacts" class="mt-16 bg-ink text-neutral-300">
    <div class="container-x grid gap-10 py-12 md:grid-cols-4">
        <div class="md:col-span-2">
            @include('partials.logo', ['dark' => true])
            <p class="mt-4 max-w-md text-sm leading-relaxed text-neutral-400">
                Оригинальные iPhone и аксессуары. Подберём модель, цвет и память, проверим при вас и ответим на любые вопросы в Telegram или WhatsApp.
            </p>
        </div>
        <div>
            <div class="mb-3 text-sm font-semibold text-white">Каталог</div>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('catalog') }}" class="hover:text-white">Все iPhone</a></li>
                <li><a href="{{ route('accessories') }}" class="hover:text-white">Аксессуары</a></li>
            </ul>
        </div>
        <div>
            <div class="mb-3 text-sm font-semibold text-white">Связаться</div>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ tg_link('Здравствуйте!') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 hover:text-white">@include('partials.icon-tg', ['class' => 'h-4 w-4']) Telegram</a></li>
                <li><a href="{{ wa_link('Здравствуйте!') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 hover:text-white">@include('partials.icon-wa', ['class' => 'h-4 w-4']) WhatsApp</a></li>
                <li><a href="tel:+{{ config('shop.telegram') }}" class="hover:text-white">{{ config('shop.phone_display') }}</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="container-x flex flex-col gap-2 py-5 text-xs text-neutral-500 sm:flex-row sm:justify-between">
            <span>© {{ date('Y') }} {{ config('shop.name') }}. Цены на сайте не являются публичной офертой.</span>
            <span>Apple, iPhone — товарные знаки Apple Inc.</span>
        </div>
    </div>
</footer>
