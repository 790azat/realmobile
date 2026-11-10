<form wire:submit="save" class="space-y-6 pb-24">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.products') }}" wire:navigate class="text-sm text-mute hover:text-ink">← Товары</a>
            <h1 class="text-2xl font-bold">{{ $product ? $product->name : 'Новый товар' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            @if ($product)
                <a href="{{ route('product', $product->slug) }}" target="_blank" class="btn-line py-2.5">На сайте ↗</a>
            @endif
            <button type="submit" class="btn-brand py-2.5" wire:loading.attr="disabled">Сохранить</button>
        </div>
    </div>

    @if (session('saved'))
        <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('saved') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl bg-brand-soft px-4 py-3 text-sm text-brand">Проверьте поля, отмеченные красным.</div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
        <div class="card space-y-4 p-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Название</label>
                    <input wire:model.blur="name" class="input" placeholder="iPhone 17 Pro">
                    @error('name')<div class="mt-1 text-sm text-brand">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="label">Категория</label>
                    <select wire:model="category_id" class="input">
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->parent_id ? 'Аксессуары / ' : '' }}{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Адрес страницы (slug)</label>
                    <input wire:model="slug" class="input" placeholder="iphone-17-pro">
                    @error('slug')<div class="mt-1 text-sm text-brand">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="label">Бейдж</label>
                    <input wire:model="badge" class="input" placeholder="NEW, ХИТ, −5%">
                </div>
            </div>
            <div>
                <label class="label">Описание</label>
                <textarea wire:model="description" rows="3" class="input"></textarea>
            </div>
            <label class="flex items-center gap-3 text-sm"><input type="checkbox" wire:model="is_active" class="h-5 w-5 accent-[#E5243B]"> Показывать на сайте</label>
        </div>

        <div class="card space-y-3 p-5">
            <div class="font-semibold">Главное фото</div>
            @if ($image)
                <img src="{{ $image }}" alt="" class="aspect-square w-full rounded-xl bg-paper object-contain p-3">
            @endif
            <input wire:model.blur="image" class="input" placeholder="Ссылка на фото или /img/iphone-17">
            <label class="btn-line w-full cursor-pointer py-2.5">
                <input type="file" wire:model="upload" accept="image/*" class="hidden"> Загрузить файл
            </label>
            <div wire:loading wire:target="upload" class="text-sm text-mute">Загружаем…</div>
            @error('image')<div class="text-sm text-brand">{{ $message }}</div>@enderror
            @error('upload')<div class="text-sm text-brand">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Варианты --}}
    <div class="card p-5">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="font-semibold">Варианты: память, цвет, цена, остаток</div>
                <div class="text-sm text-mute">У каждого варианта своя цена и количество. При 0 шт вариант показывается как «нет в наличии».</div>
            </div>
            <button type="button" wire:click="addVariant" class="btn-line py-2">+ Вариант</button>
        </div>

        <details class="mb-4 rounded-xl bg-paper p-4">
            <summary class="cursor-pointer text-sm font-medium">Создать сразу все сочетания памяти и цветов</summary>
            <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_1fr_160px_auto]">
                <input wire:model="genStorages" class="input py-2" placeholder="Память: 256 ГБ, 512 ГБ, 1 ТБ">
                <input wire:model="genColors" class="input py-2" placeholder="Цвета: Чёрный, Белый">
                <input wire:model="genPrice" type="number" class="input py-2" placeholder="Цена">
                <button type="button" wire:click="generate" class="btn-ink py-2">Создать</button>
            </div>
        </details>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] text-sm">
                <thead class="text-left text-mute">
                    <tr>
                        <th class="pb-2 pr-2 font-medium">Память</th>
                        <th class="pb-2 pr-2 font-medium">Цвет</th>
                        <th class="pb-2 pr-2 font-medium">HEX</th>
                        <th class="pb-2 pr-2 font-medium">Цена, ₽</th>
                        <th class="pb-2 pr-2 font-medium">Старая цена</th>
                        <th class="pb-2 pr-2 font-medium">Остаток</th>
                        <th class="pb-2 pr-2 font-medium">Фото цвета</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($variants as $i => $v)
                        <tr wire:key="v-{{ $v['id'] ?? 'n'.$i }}" class="align-top">
                            <td class="py-1 pr-2"><input wire:model="variants.{{ $i }}.storage" class="input w-28 px-2.5 py-2" placeholder="256 ГБ"></td>
                            <td class="py-1 pr-2"><input wire:model="variants.{{ $i }}.color" class="input w-36 px-2.5 py-2" placeholder="Чёрный"></td>
                            <td class="py-1 pr-2">
                                <div class="flex items-center gap-1.5">
                                    <input type="color" wire:model.live="variants.{{ $i }}.color_hex" class="h-9 w-9 cursor-pointer rounded-lg border border-line p-0.5">
                                    <input wire:model.live="variants.{{ $i }}.color_hex" class="input w-24 px-2 py-2 font-mono text-xs" placeholder="#000000">
                                </div>
                                @error("variants.$i.color_hex")<div class="text-xs text-brand">{{ $message }}</div>@enderror
                            </td>
                            <td class="py-1 pr-2">
                                <input type="number" wire:model="variants.{{ $i }}.price" class="input w-28 px-2.5 py-2">
                                @error("variants.$i.price")<div class="text-xs text-brand">{{ $message }}</div>@enderror
                            </td>
                            <td class="py-1 pr-2"><input type="number" wire:model="variants.{{ $i }}.old_price" class="input w-28 px-2.5 py-2"></td>
                            <td class="py-1 pr-2">
                                <div class="flex items-center">
                                    <button type="button" wire:click="$set('variants.{{ $i }}.stock', {{ max(0, (int) $v['stock'] - 1) }})" class="h-9 w-8 rounded-l-lg border border-line">−</button>
                                    <input type="number" wire:model.live="variants.{{ $i }}.stock" class="h-9 w-14 border-y border-line text-center">
                                    <button type="button" wire:click="$set('variants.{{ $i }}.stock', {{ (int) $v['stock'] + 1 }})" class="h-9 w-8 rounded-r-lg border border-line">+</button>
                                </div>
                            </td>
                            <td class="py-1 pr-2">
                                <div class="flex items-center gap-1.5">
                                    @if ($v['image'])<img src="{{ $v['image'] }}" alt="" class="h-9 w-9 rounded-lg bg-paper object-contain">@endif
                                    <input wire:model.blur="variants.{{ $i }}.image" class="input w-44 px-2.5 py-2 text-xs" placeholder="ссылка (необязательно)">
                                </div>
                            </td>
                            <td class="py-1"><button type="button" wire:click="removeVariant({{ $i }})" class="rounded-lg p-2 text-mute hover:text-brand" aria-label="Удалить">✕</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @error('variants')<div class="mt-2 text-sm text-brand">Добавьте хотя бы один вариант</div>@enderror
    </div>

    {{-- Характеристики --}}
    <div class="card p-5">
        <div class="mb-4 flex items-center justify-between">
            <div class="font-semibold">Характеристики</div>
            <button type="button" wire:click="addSpec" class="btn-line py-2">+ Строка</button>
        </div>
        <div class="space-y-2">
            @foreach ($specs as $i => $s)
                <div wire:key="s-{{ $i }}" class="grid grid-cols-[1fr_2fr_auto] gap-2">
                    <input wire:model="specs.{{ $i }}.key" class="input py-2" placeholder="Экран">
                    <input wire:model="specs.{{ $i }}.value" class="input py-2" placeholder="6,3″ OLED 120 Гц">
                    <button type="button" wire:click="removeSpec({{ $i }})" class="rounded-lg p-2 text-mute hover:text-brand">✕</button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- SEO --}}
    <div class="card grid gap-4 p-5 sm:grid-cols-2">
        <div class="sm:col-span-2 font-semibold">SEO</div>
        <div>
            <label class="label">Заголовок страницы (title)</label>
            <input wire:model="meta_title" class="input" placeholder="Купить iPhone 17 в Москве — цена | REALMOBILE">
        </div>
        <div>
            <label class="label">Описание (description)</label>
            <input wire:model="meta_description" class="input">
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
        @if ($product)
            <button type="button" wire:click="delete" wire:confirm="Удалить товар «{{ $product->name }}» вместе со всеми вариантами?" class="text-sm text-brand hover:underline">Удалить товар</button>
        @else <span></span> @endif
        <button type="submit" class="btn-brand px-8">Сохранить</button>
    </div>
</form>
