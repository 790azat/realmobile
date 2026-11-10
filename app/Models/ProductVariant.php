<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'storage', 'storage_gb', 'color', 'color_hex',
        'price', 'old_price', 'stock', 'image',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function label(): string
    {
        return collect([$this->storage, $this->color])->filter()->implode(' · ');
    }

    public function discountPercent(): ?int
    {
        if (! $this->old_price || $this->old_price <= $this->price) {
            return null;
        }

        return (int) round(100 - $this->price / $this->old_price * 100);
    }
}
