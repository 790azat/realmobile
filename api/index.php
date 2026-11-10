<?php

/*
| Точка входа для Vercel (vercel-php). Файловая система там только для чтения,
| кроме /tmp, поэтому кеши, скомпилированные шаблоны и демо-база живут в /tmp.
*/

foreach (['/tmp/views', '/tmp/storage/app/private', '/tmp/storage/app/public', '/tmp/storage/framework/cache'] as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

// Демо-режим: база SQLite лежит в репозитории и копируется в /tmp при холодном старте.
if ((getenv('DB_CONNECTION') ?: 'sqlite') === 'sqlite') {
    $db = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
    if (! file_exists($db)) {
        copy(__DIR__.'/../database/database.sqlite', $db);
    }
}

require __DIR__.'/../public/index.php';
