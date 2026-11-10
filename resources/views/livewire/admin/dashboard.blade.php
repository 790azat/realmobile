<div class="space-y-6">
    <h1 class="text-2xl font-bold">Обзор</h1>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([
            ['Заявки сегодня', $leadsToday, $leadsNew.' ждут ответа', 'text-brand'],
            ['Товаров', $productsCount, 'в каталоге', 'text-mute'],
            ['Телефонов на складе', $inStock, 'шт по всем вариантам', 'text-emerald-600'],
            ['Заканчиваются', $lowStock, $outOfStock.' вариантов закончились', 'text-amber-600'],
        ] as [$l, $v, $s, $c])
            <div class="card p-4">
                <div class="text-sm text-mute">{{ $l }}</div>
                <div class="mt-1 text-2xl font-bold">{{ $v }}</div>
                <div class="mt-0.5 text-xs {{ $c }}">{{ $s }}</div>
            </div>
        @endforeach
    </div>

    <div class="card overflow-hidden">
        <div class="flex items-center justify-between border-b border-line px-4 py-3">
            <div class="font-semibold">Последние заявки</div>
            <a href="{{ route('admin.leads') }}" wire:navigate class="text-sm text-brand">Все заявки →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-paper text-left text-mute"><tr><th class="px-4 py-2 font-medium">№</th><th class="px-4 py-2 font-medium">Клиент</th><th class="px-4 py-2 font-medium">Телефон</th><th class="px-4 py-2 font-medium">Модель</th><th class="px-4 py-2 font-medium">Статус</th><th class="px-4 py-2 font-medium">Когда</th></tr></thead>
                <tbody class="divide-y divide-line">
                    @forelse ($leads as $l)
                        <tr>
                            <td class="px-4 py-2.5">#{{ $l->id }}</td>
                            <td class="px-4 py-2.5 font-medium">{{ $l->name }}</td>
                            <td class="whitespace-nowrap px-4 py-2.5"><a href="tel:{{ $l->phone }}" class="hover:text-brand">{{ $l->phone }}</a></td>
                            <td class="px-4 py-2.5">{{ $l->product }}</td>
                            <td class="px-4 py-2.5">@include('livewire.admin.partials.status', ['status' => $l->status])</td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-mute">{{ $l->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-mute">Заявок пока нет</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="flex items-center justify-between border-b border-line px-4 py-3">
            <div class="font-semibold">Остатки по памяти</div>
            <a href="{{ route('admin.products') }}" wire:navigate class="text-sm text-brand">Все товары →</a>
        </div>
        <div class="divide-y divide-line">
            @foreach ($products as $p)
                <a href="{{ route('admin.products.edit', $p) }}" wire:navigate class="flex items-center gap-3 px-4 py-3 hover:bg-paper">
                    <img src="{{ $p->mainImage() }}" alt="" class="h-11 w-11 shrink-0 rounded-lg bg-paper object-contain">
                    <div class="min-w-0 flex-1">
                        <div class="font-medium">{{ $p->name }}</div>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach ($p->variants->groupBy('storage') as $storage => $vs)
                                @php $sum = $vs->sum('stock'); @endphp
                                <span @class(['rounded border px-1.5 py-0.5 text-xs', 'border-line' => $sum > 2, 'border-amber-300 bg-amber-50 text-amber-700' => $sum > 0 && $sum <= 2, 'border-line text-neutral-400 line-through' => $sum === 0])>{{ $storage }} · {{ $sum }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="hidden text-right font-semibold sm:block">от {{ money($p->minPrice()) }}</div>
                </a>
            @endforeach
        </div>
    </div>
</div>
