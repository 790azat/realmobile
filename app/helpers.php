<?php

if (! function_exists('money')) {
    function money(?int $value): string
    {
        if ($value === null) {
            return '';
        }

        return number_format($value, 0, '', "\u{00A0}")."\u{00A0}".config('shop.currency');
    }
}

if (! function_exists('tg_link')) {
    /** Ссылка на чат с менеджером в Telegram с готовым текстом. */
    function tg_link(string $text): string
    {
        $phone = preg_replace('/\D/', '', config('shop.telegram'));

        return 'https://t.me/+'.$phone.'?text='.rawurlencode($text);
    }
}

if (! function_exists('wa_link')) {
    function wa_link(string $text): string
    {
        $phone = preg_replace('/\D/', '', config('shop.whatsapp'));

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($text);
    }
}

if (! function_exists('stock_label')) {
    function stock_label(int $stock): array
    {
        return match (true) {
            $stock <= 0 => ['Нет в наличии', 'text-neutral-400'],
            $stock <= 2 => ["Осталось {$stock} шт", 'text-amber-600'],
            default => ["В наличии · {$stock} шт", 'text-emerald-600'],
        };
    }
}
