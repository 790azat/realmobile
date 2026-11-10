<div class="max-w-2xl space-y-5">
    <h1 class="text-2xl font-bold">Категории</h1>
    <div class="card divide-y divide-line">
        @if ($phones)
            <div class="flex items-center justify-between px-4 py-3">
                <div class="font-medium">iPhone</div>
                <a href="{{ route('admin.products', ['category' => 'iphone']) }}" wire:navigate class="text-sm text-mute hover:text-brand">{{ $phones->products_count }} товаров</a>
            </div>
        @endif
        <div class="bg-paper px-4 py-2 text-xs font-semibold uppercase tracking-wider text-mute">Аксессуары</div>
        @foreach ($cats as $c)
            <div wire:key="c-{{ $c->id }}" class="flex items-center gap-3 px-4 py-2.5">
                <input wire:model="names.{{ $c->id }}" wire:blur="rename({{ $c->id }})" class="input flex-1 py-2">
                <a href="{{ route('admin.products', ['category' => $c->slug]) }}" wire:navigate class="w-24 text-right text-sm text-mute hover:text-brand">{{ $c->products_count }} товаров</a>
                <a href="{{ route('admin.products.create', ['category' => $c->slug]) }}" wire:navigate class="btn-line px-3 py-2 text-xs">+ товар</a>
                <button wire:click="delete({{ $c->id }})" wire:confirm="Удалить категорию «{{ $c->name }}»?" class="p-2 text-mute hover:text-brand">✕</button>
            </div>
        @endforeach
    </div>
    <form wire:submit="add" class="flex gap-2">
        <input wire:model="name" class="input" placeholder="Новая категория аксессуаров, например «Кабели»">
        <button class="btn-brand shrink-0">Добавить</button>
    </form>
    @error('name')<div class="text-sm text-brand">{{ $message }}</div>@enderror
</div>
