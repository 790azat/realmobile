<x-layouts.shop :title="($current ? $current->name.' для iPhone' : 'Аксессуары для iPhone').' — купить | '.config('shop.name')"
                :description="'Чехлы, зарядки, наушники и защитные стёкла для iPhone. Подберём аксессуар под вашу модель.'">
    <div class="container-x py-8">
        <nav class="mb-3 text-sm text-mute">
            <a href="{{ route('home') }}" class="hover:text-ink">Главная</a> /
            @if ($current)
                <a href="{{ route('accessories') }}" class="hover:text-ink">Аксессуары</a> / <span class="text-ink">{{ $current->name }}</span>
            @else
                <span class="text-ink">Аксессуары</span>
            @endif
        </nav>
        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $current ? $current->name.' для iPhone' : 'Аксессуары для iPhone' }}</h1>

        <div class="mt-6">@include('partials.accessory-cats', ['cats' => $cats, 'current' => $current])</div>

        <div class="mt-8">
            @if ($products->count())
                <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    @foreach ($products as $p)
                        @include('partials.product-card', ['product' => $p, 'variants' => $p->variants])
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl bg-paper p-8 text-center sm:p-12">
                    <div class="text-xl font-bold">{{ $current ? $current->name : 'Аксессуары' }} в наличии у менеджера</div>
                    <p class="mx-auto mt-2 max-w-lg text-mute">Ассортимент аксессуаров обновляется каждый день. Напишите, для какой модели iPhone нужен аксессуар, и мы пришлём варианты с фото и ценами.</p>
                    @php $msg = 'Здравствуйте! Интересуют '.mb_strtolower($current?->name ?? 'аксессуары').' для iPhone. Что есть в наличии?'; @endphp
                    <div class="mt-6 flex flex-col justify-center gap-2 sm:flex-row">
                        <button type="button" x-data x-on:click="$dispatch('open-lead', { product: @js($current?->name ?? 'Аксессуары') })" class="btn-brand px-6">Оставить заявку</button>
                        <a href="{{ tg_link($msg) }}" target="_blank" rel="noopener" class="btn-tg px-6">@include('partials.icon-tg', ['class' => 'h-4 w-4']) Telegram</a>
                        <a href="{{ wa_link($msg) }}" target="_blank" rel="noopener" class="btn-wa px-6">@include('partials.icon-wa', ['class' => 'h-4 w-4']) WhatsApp</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.shop>
