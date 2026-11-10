<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Telegram
{
    public static function configured(): bool
    {
        return filled(config('services.telegram.bot_token')) && filled(config('services.telegram.chat_id'));
    }

    public static function sendLead(Lead $lead): bool
    {
        if (! static::configured()) {
            return false;
        }

        $e = fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES);
        $phoneDigits = preg_replace('/[^\d+]/', '', $lead->phone);
        $lines = [
            "📱 <b>Новая заявка #{$lead->id}</b>",
            '',
            '<b>Модель:</b> '.$e($lead->product ?: '—'),
            '<b>Имя:</b> '.$e($lead->name),
            '<b>Телефон:</b> '.$e($lead->phone),
        ];
        if ($lead->comment) {
            $lines[] = '<b>Комментарий:</b> '.$e($lead->comment);
        }
        if ($lead->source_url) {
            $lines[] = '';
            $lines[] = '🔗 '.$e($lead->source_url);
        }

        $payload = [
            'chat_id' => config('services.telegram.chat_id'),
            'text' => implode("\n", $lines),
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        $waDigits = ltrim($phoneDigits, '+');
        if (strlen($waDigits) >= 10) {
            $payload['reply_markup'] = json_encode(['inline_keyboard' => [[
                ['text' => '💬 WhatsApp клиента', 'url' => 'https://wa.me/'.$waDigits],
                ['text' => '🛠 Админка', 'url' => url('/admin/leads')],
            ]]]);
        }

        try {
            $res = Http::timeout(8)->asForm()->post(
                'https://api.telegram.org/bot'.config('services.telegram.bot_token').'/sendMessage',
                $payload
            );

            if (! $res->successful()) {
                Log::warning('Telegram sendMessage failed', ['status' => $res->status(), 'body' => $res->body()]);
            }

            return $res->successful();
        } catch (\Throwable $e) {
            Log::warning('Telegram sendMessage error: '.$e->getMessage());

            return false;
        }
    }
}
