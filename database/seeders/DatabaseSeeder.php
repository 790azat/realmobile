<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Lead;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $iphone = Category::create(['name' => 'iPhone', 'slug' => 'iphone', 'sort' => 1]);
        $acc = Category::create(['name' => 'Аксессуары', 'slug' => 'accessories', 'sort' => 2]);
        foreach ([
            ['Чехлы', 'cases'], ['Зарядки', 'chargers'], ['Наушники', 'headphones'], ['Защитные стёкла', 'glass'],
        ] as $i => [$name, $slug]) {
            Category::create(['name' => $name, 'slug' => $slug, 'parent_id' => $acc->id, 'sort' => $i + 1]);
        }

        // [slug, name, badge, specs, storages => [GB => price], colors => [slug => [name_ru, hex]], description]
        $models = [
            ['iphone-18-pro-max', 'iPhone 18 Pro Max', 'NEW', [
                'Экран' => '6,9″ OLED, ProMotion 120 Гц, Always-On',
                'Процессор' => 'Apple A20 Pro',
                'Основная камера' => '48 + 48 + 48 Мп, зум 4x (8x оптического качества)',
                'Фронтальная камера' => '18 Мп Center Stage',
                'Батарея' => 'до 45 ч видео',
                'Вес' => '249 г',
                'SIM' => 'eSIM',
                'Защита' => 'IP68',
            ], [256 => 139990, 512 => 159990, 1024 => 184990, 2048 => 219990],
                ['black' => ['Чёрный', '#2B2B2D'], 'silver' => ['Серебристый', '#E3E4E1'], 'glacier' => ['Ледяной', '#C9D8E2'], 'burgundy' => ['Бордовый', '#6E2B37']],
                'Самый большой и мощный iPhone 2026 года: экран 6,9″, процессор A20 Pro и три камеры по 48 Мп с 4-кратным оптическим зумом.'],
            ['iphone-18-pro', 'iPhone 18 Pro', 'NEW', [
                'Экран' => '6,3″ OLED, ProMotion 120 Гц, Always-On',
                'Процессор' => 'Apple A20 Pro',
                'Основная камера' => '48 + 48 + 48 Мп, зум 4x',
                'Фронтальная камера' => '18 Мп Center Stage',
                'Батарея' => 'до 36 ч видео',
                'Вес' => '211 г',
                'SIM' => 'eSIM',
                'Защита' => 'IP68',
            ], [256 => 124990, 512 => 144990, 1024 => 169990, 2048 => 199990],
                ['black' => ['Чёрный', '#2B2B2D'], 'silver' => ['Серебристый', '#E3E4E1'], 'glacier' => ['Ледяной', '#C9D8E2'], 'burgundy' => ['Бордовый', '#6E2B37']],
                'Флагман Pro в компактном корпусе 6,3″: A20 Pro, камера с переменной диафрагмой и новый бордовый цвет.'],
            ['iphone-17', 'iPhone 17', 'ХИТ', [
                'Экран' => '6,3″ OLED, ProMotion 120 Гц',
                'Процессор' => 'Apple A19',
                'Основная камера' => '48 Мп Fusion + 48 Мп ультраширокая',
                'Фронтальная камера' => '18 Мп Center Stage',
                'Батарея' => 'до 30 ч видео',
                'Вес' => '177 г',
                'SIM' => 'nano-SIM + eSIM',
                'Защита' => 'IP68',
            ], [256 => 74990, 512 => 92990],
                ['black' => ['Чёрный', '#353535'], 'white' => ['Белый', '#F2F2F0'], 'mist-blue' => ['Дымчато-голубой', '#9FB4CC'], 'sage' => ['Шалфей', '#A9B59A'], 'lavender' => ['Лавандовый', '#D6CCEB']],
                'Базовый iPhone 2025 года впервые с экраном 120 Гц и Always-On, двумя камерами по 48 Мп и памятью от 256 ГБ.'],
            ['iphone-air', 'iPhone Air', null, [
                'Экран' => '6,5″ OLED, ProMotion 120 Гц',
                'Процессор' => 'Apple A19 Pro',
                'Основная камера' => '48 Мп Fusion, 2x оптического качества',
                'Фронтальная камера' => '18 Мп Center Stage',
                'Батарея' => 'до 27 ч видео',
                'Вес' => '165 г',
                'Толщина' => '5,6 мм',
                'SIM' => 'eSIM',
            ], [256 => 84990, 512 => 99990, 1024 => 119990],
                ['space-black' => ['Космический чёрный', '#2E2F33'], 'cloud-white' => ['Облачно-белый', '#F1F0EC'], 'light-gold' => ['Светло-золотой', '#E9DCC3'], 'sky-blue' => ['Небесно-голубой', '#C6DAEA']],
                'Самый тонкий iPhone в истории: 5,6 мм, титановая рамка, экран 6,5″ и процессор A19 Pro.'],
            ['iphone-17e', 'iPhone 17e', null, [
                'Экран' => '6,1″ OLED Super Retina XDR',
                'Процессор' => 'Apple A19',
                'Основная камера' => '48 Мп Fusion + 12 Мп телефото 2x',
                'Фронтальная камера' => '12 Мп TrueDepth',
                'Батарея' => 'до 26 ч видео',
                'Вес' => '169 г',
                'SIM' => 'nano-SIM + eSIM',
                'Защита' => 'IP68',
            ], [256 => 57990, 512 => 72990],
                ['black' => ['Чёрный', '#2F2F31'], 'white' => ['Белый', '#F3F3F1'], 'soft-pink' => ['Нежно-розовый', '#F2D5D6']],
                'Самый доступный iPhone 2026 года с процессором A19 и памятью от 256 ГБ.'],
            ['iphone-16', 'iPhone 16', '−8%', [
                'Экран' => '6,1″ OLED Super Retina XDR',
                'Процессор' => 'Apple A18',
                'Основная камера' => '48 Мп Fusion + 12 Мп ультраширокая',
                'Фронтальная камера' => '12 Мп TrueDepth',
                'Батарея' => 'до 22 ч видео',
                'Вес' => '170 г',
                'SIM' => 'nano-SIM + eSIM',
                'Защита' => 'IP68',
            ], [128 => 59990, 256 => 69990, 512 => 84990],
                ['black' => ['Чёрный', '#3B3B3D'], 'white' => ['Белый', '#F4F4F2'], 'pink' => ['Розовый', '#F3C6D6'], 'teal' => ['Бирюзовый', '#A9D3CF'], 'ultramarine' => ['Ультрамарин', '#8DA2F2']],
                'iPhone с кнопкой «Камера», Apple Intelligence и процессором A18 по выгодной цене.'],
            ['iphone-15', 'iPhone 15', null, [
                'Экран' => '6,1″ OLED Super Retina XDR, Dynamic Island',
                'Процессор' => 'Apple A16 Bionic',
                'Основная камера' => '48 Мп + 12 Мп ультраширокая',
                'Фронтальная камера' => '12 Мп TrueDepth',
                'Батарея' => 'до 20 ч видео',
                'Вес' => '171 г',
                'SIM' => 'nano-SIM + eSIM',
                'Разъём' => 'USB-C',
            ], [128 => 52990, 256 => 61990, 512 => 76990],
                ['black' => ['Чёрный', '#3A3B3D'], 'blue' => ['Голубой', '#D3DEE6'], 'green' => ['Зелёный', '#D7E3D2'], 'yellow' => ['Жёлтый', '#F5EBC6'], 'pink' => ['Розовый', '#F5DCE0']],
                'Dynamic Island, камера 48 Мп и разъём USB-C в матовом цветном стекле.'],
            ['iphone-14', 'iPhone 14', null, [
                'Экран' => '6,1″ OLED Super Retina XDR',
                'Процессор' => 'Apple A15 Bionic',
                'Основная камера' => '12 Мп + 12 Мп ультраширокая',
                'Фронтальная камера' => '12 Мп TrueDepth',
                'Батарея' => 'до 20 ч видео',
                'Вес' => '172 г',
                'SIM' => 'nano-SIM + eSIM',
                'Разъём' => 'Lightning',
            ], [128 => 44990, 256 => 52990],
                ['midnight' => ['Тёмная ночь', '#2E333A'], 'starlight' => ['Сияющая звезда', '#F2EDE4'], 'purple' => ['Фиолетовый', '#D9CFE3'], 'blue' => ['Синий', '#A8BDD0'], 'red' => ['(PRODUCT)RED', '#C8102E'], 'yellow' => ['Жёлтый', '#F7E38C']],
                'Проверенный iPhone с отличной камерой и автономностью по самой доступной цене.'],
        ];

        foreach ($models as $sort => [$slug, $name, $badge, $specs, $storages, $colors, $desc]) {
            $product = Product::create([
                'category_id' => $iphone->id,
                'name' => $name,
                'slug' => $slug,
                'family' => $name,
                'description' => $desc,
                'specs' => $specs + ['Гарантия' => '12 месяцев'],
                'badge' => $badge,
                // Цветной рендер Apple легче (~150 КБ), чем общее фото модели (до 900 КБ).
                'image' => "/img/{$slug}".(['iphone-18-pro-max' => '_burgundy', 'iphone-18-pro' => '_glacier', 'iphone-17' => '_lavender', 'iphone-air' => '_sky-blue', 'iphone-16' => '_ultramarine', 'iphone-15' => '_pink', 'iphone-14' => '_purple'][$slug] ?? ''),
                'sort' => $sort + 1,
                'meta_title' => "Купить {$name} — цена, наличие | REALMOBILE",
                'meta_description' => "{$name}: оригинал, все цвета и объёмы памяти. Цена от ".number_format(min($storages), 0, '', ' ')." ₽. Оставьте заявку или напишите в Telegram/WhatsApp.",
            ]);

            $ci = 0;
            foreach ($colors as $colorSlug => [$colorName, $hex]) {
                foreach ($storages as $gb => $price) {
                    // Детерминированные остатки для демо: часть вариантов закончилась.
                    $stock = (crc32($slug.$colorSlug.$gb) % 9);
                    $stock = $stock === 0 ? 0 : max(1, $stock - 1);
                    $product->variants()->create([
                        'storage' => $gb >= 1024 ? ($gb / 1024).' ТБ' : $gb.' ГБ',
                        'storage_gb' => $gb,
                        'color' => $colorName,
                        'color_hex' => $hex,
                        'price' => $price,
                        'old_price' => $badge === '−8%' ? (int) (round($price / 0.92 / 1000) * 1000 - 10) : null,
                        'stock' => $stock,
                        'image' => "/img/{$slug}_{$colorSlug}",
                    ]);
                }
                $ci++;
            }
        }

        Lead::create(['name' => 'Дмитрий', 'phone' => '+7 916 000-00-00', 'product' => 'iPhone 17 · 256 ГБ · Лавандовый', 'status' => 'new', 'telegram_sent' => true]);
        Lead::create(['name' => 'Анна', 'phone' => '+7 905 000-00-00', 'product' => 'iPhone 18 Pro Max · 512 ГБ · Бордовый', 'status' => 'call', 'telegram_sent' => true]);
    }
}
