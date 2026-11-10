<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;

class Catalog extends Component
{
    #[Url(as: 'model', except: [])]
    public array $models = [];

    #[Url(as: 'mem', except: [])]
    public array $storages = [];

    #[Url(as: 'from', except: null)]
    public ?int $priceFrom = null;

    #[Url(as: 'to', except: null)]
    public ?int $priceTo = null;

    #[Url(as: 'stock', except: false)]
    public bool $inStock = false;

    #[Url(as: 'sort', except: 'new')]
    public string $sort = 'new';

    public bool $embedded = false;

    public function toggle(string $field, string $value): void
    {
        $list = $this->{$field};
        $this->{$field} = in_array($value, $list, true)
            ? array_values(array_diff($list, [$value]))
            : [...$list, $value];
    }

    public function resetFilters(): void
    {
        $this->reset('models', 'storages', 'priceFrom', 'priceTo', 'inStock');
    }

    public function render()
    {
        $all = Product::with('variants')
            ->where('is_active', true)
            ->whereHas('category', fn ($q) => $q->where('slug', 'iphone'))
            ->orderBy('sort')
            ->get();

        $allVariants = $all->flatMap->variants;
        $storageOptions = $allVariants->sortBy('storage_gb')->pluck('storage')->unique()->values();
        $bounds = [(int) $allVariants->min('price'), (int) $allVariants->max('price')];

        $items = $all
            ->filter(fn ($p) => ! $this->models || in_array($p->slug, $this->models, true))
            ->map(function ($p) {
                $matching = $p->variants->filter(function ($v) {
                    return (! $this->storages || in_array($v->storage, $this->storages, true))
                        && (! $this->priceFrom || $v->price >= $this->priceFrom)
                        && (! $this->priceTo || $v->price <= $this->priceTo)
                        && (! $this->inStock || $v->stock > 0);
                });

                return $matching->isEmpty() ? null : ['product' => $p, 'variants' => $matching];
            })
            ->filter()
            ->values();

        $items = match ($this->sort) {
            'cheap' => $items->sortBy(fn ($i) => $i['variants']->min('price'))->values(),
            'expensive' => $items->sortByDesc(fn ($i) => $i['variants']->min('price'))->values(),
            default => $items,
        };

        $view = view('livewire.catalog', [
            'all' => $all,
            'items' => $items,
            'storageOptions' => $storageOptions,
            'bounds' => $bounds,
            'activeCount' => count($this->models) + count($this->storages) + ($this->priceFrom ? 1 : 0) + ($this->priceTo ? 1 : 0) + ($this->inStock ? 1 : 0),
        ]);

        return $this->embedded ? $view : $view->layout('layouts.app', [
            'title' => trim('Купить iPhone '.config('shop.city_in')).' — цены и наличие | '.config('shop.name'),
            'description' => 'Каталог оригинальных iPhone: 18 Pro Max, 18 Pro, 17, Air, 17e, 16, 15, 14. Фильтр по модели, памяти и цене, наличие по каждому цвету.',
        ]);
    }
}
