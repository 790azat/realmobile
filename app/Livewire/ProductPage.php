<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProductPage extends Component
{
    public Product $product;

    #[Url(as: 'mem')]
    public ?string $storage = null;

    #[Url(as: 'color')]
    public ?string $color = null;

    public function mount(string $slug): void
    {
        $this->product = Product::with('variants', 'category')
            ->where('slug', $slug)->where('is_active', true)->firstOrFail();

        $default = $this->product->defaultVariant();
        $this->storage ??= $default?->storage;
        $this->color ??= $default?->color;
    }

    public function selectStorage(string $storage): void
    {
        $this->storage = $storage;
        // Если в этом объёме нет выбранного цвета, берём первый доступный.
        if (! $this->current()) {
            $this->color = $this->product->variants->where('storage', $storage)->sortByDesc('stock')->first()?->color;
        }
    }

    public function selectColor(string $color): void
    {
        $this->color = $color;
        if (! $this->current()) {
            $this->storage = $this->product->variants->where('color', $color)->sortByDesc('stock')->first()?->storage;
        }
    }

    public function current()
    {
        return $this->product->variants->first(
            fn ($v) => $v->storage == $this->storage && $v->color == $this->color
        ) ?? ($this->product->variants->count() === 1 ? $this->product->variants->first() : null);
    }

    public function render()
    {
        $p = $this->product;
        $variant = $this->current() ?? $p->defaultVariant();
        $label = trim($p->name.' '.($variant ? '· '.$variant->label() : ''));
        $message = "Здравствуйте! Интересует {$label}".($variant ? ' за '.money($variant->price) : '').'. Есть в наличии?';

        $similar = Product::with('variants')->where('is_active', true)
            ->where('category_id', $p->category_id)->where('id', '!=', $p->id)
            ->orderByRaw('abs(sort - ?)', [$p->sort])->limit(4)->get();

        $image = $variant?->image ?: $p->mainImage();
        $min = $p->variants->min('price');

        return view('livewire.product-page', [
            'variant' => $variant,
            'label' => $label,
            'message' => $message,
            'similar' => $similar,
            'image' => $image,
        ])->layout('layouts.app', [
            'title' => $p->meta_title ?: "Купить {$p->name} — цена от ".money($min).' | '.config('shop.name'),
            'description' => $p->meta_description ?: \Illuminate\Support\Str::limit($p->description, 155),
            'canonical' => route('product', $p->slug),
            'ogImage' => url($p->mainImage()),
        ]);
    }
}
