<?php

/*
| Официальные изображения iPhone (Apple). Ключ используется в URL /img/{key}.
| Для каждого ключа перечислены источники по порядку: берётся первый, который
| отдаёт настоящее изображение. Последний источник у каждого цвета — проверенное
| фото модели со страницы технических характеристик support.apple.com, поэтому
| пустых картинок не бывает.
*/

$apple = 'https://cdsassets.apple.com/live/7WUAS350/images/tech-specs/';
$store = fn (string $name) => "https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/{$name}?wid=800&hei=800&fmt=png-alpha";

$hero = [
    'iphone-18-pro-max' => $apple.'iphone-18-pro-max.png',
    'iphone-18-pro' => $apple.'iphone-18-pro.png',
    'iphone-17' => $apple.'iphone-17-hero.png',
    'iphone-air' => $apple.'iphone-air-hero.png',
    'iphone-17e' => $apple.'iphone-17e.png',
    'iphone-16' => $apple.'iphone-16.png',
    'iphone-15' => $apple.'iphone_15_hero.png',
    'iphone-14' => 'https://cdsassets.apple.com/live/SZLF0YNV/images/sp/111850_iphone-14_1.png',
];

// slug модели => [префикс имени в Apple Store CDN, [slug цвета => код цвета Apple]]
$finishes = [
    'iphone-18-pro-max' => ['iphone-18-pro-finish-select-202609-6-9inch-', ['black' => 'black', 'silver' => 'silver', 'glacier' => 'glacier', 'burgundy' => 'burgundy']],
    'iphone-18-pro' => ['iphone-18-pro-finish-select-202609-6-3inch-', ['black' => 'black', 'silver' => 'silver', 'glacier' => 'glacier', 'burgundy' => 'burgundy']],
    'iphone-17' => ['iphone-17-finish-select-202509-', ['black' => 'black', 'white' => 'white', 'mist-blue' => 'mistblue', 'sage' => 'sage', 'lavender' => 'lavender']],
    'iphone-air' => ['iphone-air-finish-select-202509-', ['space-black' => 'spaceblack', 'cloud-white' => 'cloudwhite', 'light-gold' => 'lightgold', 'sky-blue' => 'skyblue']],
    'iphone-17e' => ['iphone-17e-finish-select-202602-', ['black' => 'black', 'white' => 'white', 'soft-pink' => 'softpink']],
    'iphone-16' => ['iphone-16-finish-select-202409-6-1inch-', ['black' => 'black', 'white' => 'white', 'pink' => 'pink', 'teal' => 'teal', 'ultramarine' => 'ultramarine']],
    'iphone-15' => ['iphone-15-finish-select-202309-6-1inch-', ['black' => 'black', 'blue' => 'blue', 'green' => 'green', 'yellow' => 'yellow', 'pink' => 'pink']],
    'iphone-14' => ['iphone-14-finish-select-202209-6-1inch-', ['midnight' => 'midnight', 'starlight' => 'starlight', 'purple' => 'purple', 'blue' => 'blue', 'red' => 'product-red', 'yellow' => 'yellow']],
];

$map = [];
foreach ($hero as $model => $url) {
    $map[$model] = [$url];
}
foreach ($finishes as $model => [$prefix, $colors]) {
    foreach ($colors as $slug => $code) {
        $map["{$model}_{$slug}"] = [$store($prefix.$code), $hero[$model]];
    }
}

return $map;
