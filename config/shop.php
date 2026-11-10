<?php

return [
    'name' => env('SHOP_NAME', 'REALMOBILE'),
    'city_in' => env('SHOP_CITY_IN', ''), // например «в Москве»; пусто = без города
    'currency' => env('SHOP_CURRENCY', '₽'),
    'telegram' => env('MANAGER_TELEGRAM', '79163774630'),
    'whatsapp' => env('MANAGER_WHATSAPP', '79060010090'),
    'phone_display' => env('SHOP_PHONE_DISPLAY', '+7 916 377-46-30'),
    'admin_login' => env('ADMIN_LOGIN', 'admin'),
    'admin_password' => env('ADMIN_PASSWORD'),
];
