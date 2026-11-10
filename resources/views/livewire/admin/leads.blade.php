<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold">Заявки</h1>
        <input type="search" wire:model.live.debounce.400ms="q" class="input max-w-xs py-2" placeholder="Поиск: имя, телефон, модель">
    </div>
    <div class="flex flex-wrap gap-2">
        <button wire:click="$set('status', '')" @class(['chip', 'chip-on' => $status === ''])>Все · {{ $counts->sum() }}</button>
        @foreach (\App\Models\Lead::STATUSES as $k => $label)
            <button wire:click="$set('status', '{{ $k }}')" @class(['chip', 'chip-on' => $status === $k])>{{ $label }} · {{ $counts[$k] ?? 0 }}</button>
        @endforeach
    </div>

    @unless (\App\Services\Telegram::configured())
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Telegram-бот ещё не подключён: заявки сохраняются здесь, но не уходят менеджеру. Укажите <code>TELEGRAM_BOT_TOKEN</code> и <code>TELEGRAM_CHAT_ID</code> в настройках окружения.
        </div>
    @endunless

    <div class="space-y-3">
        @forelse ($leads as $l)
            <div wire:key="lead-{{ $l->id }}" class="card flex flex-col gap-3 p-4 md:flex-row md:items-center">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm text-mute">#{{ $l->id }}</span>
                        <span class="font-semibold">{{ $l->name }}</span>
                        @include('livewire.admin.partials.status', ['status' => $l->status])
                        @if ($l->telegram_sent)<span class="text-xs text-[#229ED9]">✓ в Telegram</span>@endif
                    </div>
                    <div class="mt-1 text-sm">{{ $l->product }}</div>
                    <div class="mt-1 text-xs text-mute">{{ $l->created_at->format('d.m.Y H:i') }}</div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="tel:{{ $l->phone }}" class="btn-line py-2">{{ $l->phone }}</a>
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $l->phone) }}" target="_blank" class="btn-wa px-3 py-2" aria-label="WhatsApp">@include('partials.icon-wa', ['class' => 'h-4 w-4'])</a>
                    <select wire:change="setStatus({{ $l->id }}, $event.target.value)" class="rounded-lg border border-line bg-white py-2 pl-3 pr-8 text-sm">
                        @foreach (\App\Models\Lead::STATUSES as $k => $label)
                            <option value="{{ $k }}" @selected($l->status === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button wire:click="delete({{ $l->id }})" wire:confirm="Удалить заявку #{{ $l->id }}?" class="rounded-lg p-2 text-mute hover:text-brand" aria-label="Удалить">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="card p-8 text-center text-mute">Заявок нет</div>
        @endforelse
    </div>
    {{ $leads->links() }}
</div>
