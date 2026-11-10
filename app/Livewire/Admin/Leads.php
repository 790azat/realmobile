<?php

namespace App\Livewire\Admin;

use App\Models\Lead;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Заявки')]
class Leads extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    #[Url]
    public string $q = '';

    public function updated(): void
    {
        $this->resetPage();
    }

    public function setStatus(int $id, string $status): void
    {
        abort_unless(array_key_exists($status, Lead::STATUSES), 422);
        Lead::whereKey($id)->update(['status' => $status]);
        $this->dispatch('toast', message: 'Статус обновлён');
    }

    public function delete(int $id): void
    {
        Lead::whereKey($id)->delete();
    }

    public function render()
    {
        $leads = Lead::query()
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->q, fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$this->q}%")->orWhere('phone', 'like', "%{$this->q}%")->orWhere('product', 'like', "%{$this->q}%")))
            ->latest()->paginate(20);

        return view('livewire.admin.leads', [
            'leads' => $leads,
            'counts' => Lead::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }
}
