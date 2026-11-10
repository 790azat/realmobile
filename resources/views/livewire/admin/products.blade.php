<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold">Товары и остатки</h1>
        <a href="{{ route('admin.products.create') }}" wire:navigate class="btn-brand py-2.5">+ Добавить товар</a>
    </div>
    <div class="flex flex-wrap gap-2">
        <button wire:click="$set('category', '')" @class(['chip', 'chip-on' => $category === ''])>Все</button>
        @foreach ($categories as $c)
            <button wire:click="$set('category', '{{ $c->slug }}')" @class(['chip', 'chip-on' => $category === $c->slug])>{{ $c->name }}</button>
        @endforeach
    </div>

    <div class="card divide-y divide-line overflow-hidden">
        @forelse ($products as $p)
            <div wire:key="prod-{{ $p->id }}" class="flex items-center gap-3 px-4 py-3">
                <img src="{{ $p->mainImage() }}" alt="" class="h-12 w-12 shrink-0 rounded-lg bg-paper object-contain">
                <a href="{{ route('admin.products.edit', $p) }}" wire:navigate class="min-w-0 flex-1">
                    <div class="font-medium hover:text-brand">{{ $p->name }}</div>
                    <div class="text-xs text-mute">{{ $p->category?->name }} · {{ $p->variants->count() }} {{ trans_choice('вариант|варианта|вариантов', $p->variants->count()) }} · {{ $p->totalStock() }} шт</div>
                </a>
                <div class="hidden w-32 text-right font-semibold sm:block">{{ $p->variants->count() ? 'от '.money($p->minPrice()) : '—' }}</div>
                <button wire:click="toggle({{ $p->id }})" title="Показывать на сайте"
                    @class(['relative h-6 w-11 shrink-0 rounded-full transition', 'bg-emerald-500' => $p->is_active, 'bg-neutral-300' => ! $p->is_active])>
                    <span @class(['absolute top-0.5 h-5 w-5 rounded-full bg-white shadow transition', 'left-[22px]' => $p->is_active, 'left-0.5' => ! $p->is_active])></span>
                </button>
            </div>
        @empty
            <div class="p-8 text-center text-mute">В этой категории пока нет товаров. <a href="{{ route('admin.products.create') }}" wire:navigate class="text-brand">Добавить первый</a></div>
        @endforelse
    </div>
</div>
