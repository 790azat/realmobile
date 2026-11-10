<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Товары')]
class Products extends Component
{
    #[Url]
    public string $category = '';

    public function toggle(int $id): void
    {
        $p = Product::findOrFail($id);
        $p->update(['is_active' => ! $p->is_active]);
    }

    public function render()
    {
        return view('livewire.admin.products', [
            'categories' => Category::orderBy('parent_id')->orderBy('sort')->get()->filter(fn ($c) => $c->slug !== 'accessories'),
            'products' => Product::with('variants', 'category')
                ->when($this->category, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->category)))
                ->orderBy('category_id')->orderBy('sort')->get(),
        ]);
    }
}
