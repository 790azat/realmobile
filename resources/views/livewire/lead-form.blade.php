<div>
    @if ($open)
        <div class="fixed inset-0 z-[60] flex items-end justify-center bg-black/50 sm:items-center sm:p-4"
             x-data x-on:keydown.escape.window="$wire.set('open', false)" wire:click.self="$set('open', false)">
            <div class="w-full max-w-md rounded-t-3xl bg-white p-6 shadow-2xl sm:rounded-3xl" style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom))">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xl font-bold">{{ $sent ? 'Заявка отправлена' : 'Оставить заявку' }}</div>
                        @unless ($sent)<div class="mt-1 text-sm text-mute">Менеджер перезвонит или напишет в течение 15 минут</div>@endunless
                    </div>
                    <button type="button" wire:click="$set('open', false)" class="-m-2 rounded-lg p-2 text-mute hover:text-ink" aria-label="Закрыть">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>

                @if ($sent)
                    <div class="rounded-2xl bg-emerald-50 p-4 text-emerald-800">
                        Спасибо! Мы получили заявку на <b>{{ $product }}</b> и скоро свяжемся с вами.
                    </div>
                    <button type="button" wire:click="$set('open', false)" class="btn-ink mt-4 w-full">Хорошо</button>
                @else
                    <form wire:submit="submit" class="space-y-3.5">
                        <div>
                            <label class="label" for="lead-name">Имя</label>
                            <input id="lead-name" type="text" wire:model="name" autocomplete="name" class="input" placeholder="Как к вам обращаться">
                            @error('name')<div class="mt-1 text-sm text-brand">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="label" for="lead-phone">Телефон</label>
                            <input id="lead-phone" type="tel" wire:model="phone" autocomplete="tel" inputmode="tel" class="input" placeholder="+7 900 000-00-00">
                            @error('phone')<div class="mt-1 text-sm text-brand">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="label" for="lead-product">Модель</label>
                            <input id="lead-product" type="text" wire:model="product" list="lead-models" class="input" placeholder="Например, iPhone 17 256 ГБ">
                            <datalist id="lead-models">
                                @foreach ($models as $m)<option value="{{ $m }}">@endforeach
                            </datalist>
                            @error('product')<div class="mt-1 text-sm text-brand">{{ $message }}</div>@enderror
                        </div>
                        <input type="text" wire:model="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <button type="submit" class="btn-brand w-full py-3.5" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit">Отправить заявку</span>
                            <span wire:loading wire:target="submit">Отправляем…</span>
                        </button>
                        <p class="text-center text-xs text-mute">Нажимая кнопку, вы соглашаетесь на обработку персональных данных</p>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
