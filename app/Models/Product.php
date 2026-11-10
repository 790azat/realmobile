<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'family', 'description', 'specs', 'badge',
        'image', 'is_active', 'sort', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'specs' => 'array',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('storage_gb')->orderBy('id');
    }

    public function minPrice(): ?int
    {
        return $this->variants->min('price');
    }

    public function totalStock(): int
    {
        return (int) $this->variants->sum('stock');
    }

    /** Варианты, которые стоит показать первыми: в наличии, самый дешёвый. */
    public function defaultVariant(): ?ProductVariant
    {
        return $this->variants->sortBy([['stock', 'desc'], ['price', 'asc']])
            ->first(fn ($v) => $v->stock > 0) ?? $this->variants->sortBy('price')->first();
    }

    public function mainImage(): string
    {
        return $this->image ?: ($this->variants->firstWhere('image')?->image ?? '/images/logo-mark.svg');
    }

    public function colors()
    {
        return $this->variants->filter(fn ($v) => $v->color)->unique('color')->values();
    }

    public function storages()
    {
        return $this->variants->filter(fn ($v) => $v->storage)->unique('storage')->sortBy('storage_gb')->values();
    }

    public function isPhone(): bool
    {
        return $this->category?->slug === 'iphone';
    }
}
