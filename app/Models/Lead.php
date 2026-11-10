<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    public const STATUSES = [
        'new' => 'Новая',
        'call' => 'Ждёт звонка',
        'done' => 'Продано',
        'cancelled' => 'Отказ',
    ];

    protected $fillable = [
        'name', 'phone', 'product', 'product_id', 'variant_id', 'comment',
        'status', 'telegram_sent', 'source_url',
    ];

    protected $casts = ['telegram_sent' => 'boolean'];

    public function productModel(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
