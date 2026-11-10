<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Отдаёт официальные фото iPhone с нашего домена и кеширует их на CDN.
 * Перебирает источники из config/phone_images.php и берёт первый рабочий.
 */
class ImageController extends Controller
{
    private const MIN_BYTES = 15000;

    public function show(Request $request, string $key)
    {
        $sources = config("phone_images.{$key}");
        abort_unless($sources, 404);

        $dir = sys_get_temp_dir().'/rm-img';
        @mkdir($dir, 0775, true);
        $cache = $dir.'/'.md5($key);

        $report = [];
        if (! $request->boolean('debug') && is_file($cache) && is_file($cache.'.type')) {
            return $this->respond(file_get_contents($cache), file_get_contents($cache.'.type'));
        }

        foreach ($sources as $url) {
            try {
                $res = Http::timeout(8)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130 Safari/537.36',
                    'Accept' => 'image/avif,image/webp,image/png,image/*,*/*;q=0.8',
                ])->get($url);
                $type = (string) $res->header('Content-Type');
                $ok = $res->successful() && str_starts_with($type, 'image/') && strlen($res->body()) >= self::MIN_BYTES;
                $report[] = ['url' => $url, 'status' => $res->status(), 'type' => $type, 'bytes' => strlen($res->body()), 'ok' => $ok];
                if ($ok) {
                    if ($request->boolean('debug')) {
                        return response()->json($report);
                    }
                    @file_put_contents($cache, $res->body());
                    @file_put_contents($cache.'.type', $type);

                    return $this->respond($res->body(), $type);
                }
            } catch (\Throwable $e) {
                $report[] = ['url' => $url, 'error' => $e->getMessage()];
            }
        }

        if ($request->boolean('debug')) {
            return response()->json($report, 502);
        }

        // Все источники недоступны: перенаправляем браузер на последний (проверенный) URL.
        return redirect()->away(end($sources));
    }

    /** Диагностика: какой источник сработал для каждого фото. */
    public function status()
    {
        $map = config('phone_images');
        $urls = [];
        foreach ($map as $key => $sources) {
            foreach ($sources as $i => $url) {
                $urls["{$key}#{$i}"] = $url;
            }
        }
        // Контроль: заведомо несуществующее имя, чтобы убедиться, что CDN не отдаёт заглушку.
        $urls['__bogus#0'] = 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-99-finish-select-209901-nonexistent?wid=1000&hei=1000&fmt=png-alpha';

        $responses = Http::pool(fn ($pool) => collect($urls)->map(
            fn ($url, $id) => $pool->as($id)->timeout(8)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url)
        )->all());

        $out = [];
        foreach ($urls as $id => $url) {
            [$key, $i] = explode('#', $id);
            $r = $responses[$id] ?? null;
            $ok = $r instanceof \Illuminate\Http\Client\Response && $r->successful()
                && str_starts_with((string) $r->header('Content-Type'), 'image/') && strlen($r->body()) >= self::MIN_BYTES;
            $out[$key][] = $ok ? 'ok:'.strlen($r->body()) : 'fail:'.($r instanceof \Illuminate\Http\Client\Response ? $r->status() : 'err');
        }

        return response()->json($out);
    }

    private function respond(string $body, string $type)
    {
        return response($body, 200, [
            'Content-Type' => $type,
            'Cache-Control' => 'public, max-age=2592000, s-maxage=31536000, immutable',
        ]);
    }
}
