@php
    $icons = [
        'cases' => '<rect x="7" y="2.5" width="10" height="19" rx="2.6"/><rect x="9" y="4.6" width="3.2" height="3.2" rx="1"/>',
        'chargers' => '<path d="M9 2v4M15 2v4"/><rect x="6" y="6" width="12" height="8" rx="2"/><path d="M12 14v3a3 3 0 0 1-3 3H8"/>',
        'headphones' => '<path d="M4 15v-3a8 8 0 0 1 16 0v3"/><rect x="3" y="14" width="5" height="7" rx="2"/><rect x="16" y="14" width="5" height="7" rx="2"/>',
        'glass' => '<rect x="6" y="2.5" width="12" height="19" rx="2.6"/><path d="M9 7l3-3M9 11l6-6M10 15l5-5"/>',
    ];
@endphp
<div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4">
    @foreach ($cats as $c)
        <a href="{{ route('accessories.category', $c->slug) }}" @class(['card group flex flex-col justify-between gap-6 p-5 transition hover:-translate-y-0.5 hover:shadow-lg', 'border-brand ring-1 ring-brand' => isset($current) && $current?->id === $c->id])>
            @if ($c->image)
                <img src="{{ $c->image }}" alt="{{ $c->name }}" loading="lazy" class="h-24 w-full object-contain">
            @else
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-soft text-brand transition group-hover:bg-brand group-hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">{!! $icons[$c->slug] ?? $icons['cases'] !!}</svg>
                </span>
            @endif
            <div>
                <div class="font-semibold">{{ $c->name }}</div>
                <div class="text-sm text-mute">{{ $c->products_count ? $c->products_count.' '.trans_choice('товар|товара|товаров', $c->products_count) : 'Под заказ' }}</div>
            </div>
        </a>
    @endforeach
</div>
