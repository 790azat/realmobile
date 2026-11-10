@php $map = ['new' => 'bg-brand-soft text-brand', 'call' => 'bg-amber-50 text-amber-700', 'done' => 'bg-emerald-50 text-emerald-700', 'cancelled' => 'bg-neutral-100 text-neutral-500']; @endphp
<span class="rounded-md px-2 py-0.5 text-xs font-semibold {{ $map[$status] ?? '' }}">{{ \App\Models\Lead::STATUSES[$status] ?? $status }}</span>
