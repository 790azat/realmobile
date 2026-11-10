<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Категории')]
class Categories extends Component
{
    public string $name = '';

    public array $names = [];

    public function mount(): void
    {
        $this->names = Category::pluck('name', 'id')->all();
    }

    public function add(): void
    {
        $this->validate(['name' => 'required|string|max:60']);
        $root = Category::accessoriesRoot();
        $slug = Str::slug($this->name) ?: 'cat-'.Str::random(5);
        while (Category::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(3));
        }
        $c = Category::create(['name' => $this->name, 'slug' => $slug, 'parent_id' => $root?->id, 'sort' => (int) Category::max('sort') + 1]);
        $this->names[$c->id] = $c->name;
        $this->reset('name');
    }

    public function rename(int $id): void
    {
        $name = trim($this->names[$id] ?? '');
        if ($name !== '') {
            Category::whereKey($id)->update(['name' => $name]);
            $this->dispatch('toast', message: 'Сохранено');
        }
    }

    public function delete(int $id): void
    {
        $c = Category::withCount('products')->findOrFail($id);
        if ($c->products_count || in_array($c->slug, ['iphone', 'accessories'])) {
            $this->dispatch('toast', message: 'Сначала перенесите или удалите товары категории');

            return;
        }
        $c->delete();
        unset($this->names[$id]);
    }

    public function render()
    {
        return view('livewire.admin.categories', [
            'phones' => Category::withCount('products')->where('slug', 'iphone')->first(),
            'cats' => Category::withCount('products')->where('parent_id', Category::accessoriesRoot()?->id)->orderBy('sort')->get(),
        ]);
    }
}
