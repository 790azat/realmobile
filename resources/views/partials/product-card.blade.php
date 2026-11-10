@php
    $variants = $variants ?? $product->variants;
    $cheapest = $variants->sortBy('price')->first();
    $stock = (int) $variants->sum('stock');
    [$stockText, $stockClass] = stock_label($stock);
    $colors = $variants->unique('color')->filter(fn ($v) => $v->color_hex);
    $storagesList = $variants->sortBy('storage_gb')->pluck('storage')->filter()->unique();
@endphp
<a href="{{ route('product', $product->slug) }}" wire:key="p-{{ $product->id }}" class="card group relative flex flex-col overflow-hidden p-3 transition hover:-translate-y-0.5 hover:shadow-lg sm:p-4">
    @if ($product->badge)
        <span class="badge absolute left-3 top-3 z-10">{{ $product->badge }}</span>
    @endif
    <div class="flex aspect-square items-center justify-center overflow-hidden rounded-xl bg-paper">
        <img src="{{ $product->mainImage() }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="600" height="600"
             class="h-full w-full object-contain p-3 transition duration-300 group-hover:scale-105">
    </div>
    <div class="mt-3 flex flex-1 flex-col">
        <div class="font-semibold leading-tight sm:text-lg">{{ $product->name }}</div>
        <div class="mt-1 text-xs text-mute sm:text-sm">{{ $storagesList->implode(' · ') }}</div>
        @if ($colors->count())
            <div class="mt-2 flex flex-wrap gap-1.5">
                @foreach ($colors->take(6) as $c)
                    <span class="h-3.5 w-3.5 rounded-full ring-1 ring-black/10" style="background: {{ $c->color_hex }}" title="{{ $c->color }}"></span>
                @endforeach
            </div>
        @endif
        <div class="mt-auto pt-3">
            <div class="flex flex-wrap items-baseline gap-x-2">
                <span class="text-lg font-bold sm:text-xl">{{ $variants->count() > 1 ? 'от ' : '' }}{{ money($cheapest->price) }}</span>
                @if ($cheapest->old_price)
                    <span class="text-xs text-mute line-through sm:text-sm">{{ money($cheapest->old_price) }}</span>
                @endif
            </div>
            <div class="mt-1 text-xs {{ $stockClass }}">● {{ $stockText }}</div>
            <span class="btn-ink mt-3 w-full py-2.5">Подробнее</span>
        </div>
    </div>
</a>
