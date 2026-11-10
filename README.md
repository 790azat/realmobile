# REALMOBILE — сайт магазина iPhone

Laravel 13 + Livewire 4 + Tailwind 4. Деплой на Vercel через `vercel-php`.

## Что есть
- Каталог iPhone (18 Pro Max, 18 Pro, 17, Air, 17e, 16, 15, 14) с официальными фото Apple, характеристиками, ценой и остатком по каждому сочетанию памяти и цвета.
- Фильтры: модель, память, цена, «только в наличии». Фильтры сохраняются в адресе страницы.
- На карточке 3 кнопки: «Оставить заявку» (форма → Telegram-бот менеджеру), «Telegram» и «WhatsApp» с готовым текстом о выбранной модели.
- Аксессуары: категории (чехлы, зарядки, наушники, стёкла), товары добавляются через админку.
- Админка `/admin`: товары и варианты, остатки, заявки со статусами, категории.
- SEO: title/description для каждой страницы, Open Graph, JSON-LD (Store, Product), `sitemap.xml`, `robots.txt`.

## Переменные окружения
| Переменная | Назначение |
|---|---|
| `APP_KEY` | ключ Laravel (`php artisan key:generate --show`) |
| `APP_URL` | боевой адрес сайта, например `https://realmobile.ru` |
| `ADMIN_LOGIN`, `ADMIN_PASSWORD` | вход в админку |
| `TELEGRAM_BOT_TOKEN` | токен бота от @BotFather |
| `TELEGRAM_CHAT_ID` | id чата/группы менеджера, куда приходят заявки |
| `MANAGER_TELEGRAM` | номер менеджера для кнопки Telegram (по умолчанию 79163774630) |
| `MANAGER_WHATSAPP` | номер для кнопки WhatsApp (по умолчанию 79060010090) |
| `SHOP_CITY_IN` | город для заголовков, например «в Москве» (необязательно) |

## Локальный запуск
```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate --seed
php artisan serve
```

## Фото телефонов
Официальные изображения Apple отдаются через `/img/{ключ}` (см. `config/phone_images.php`): сервер берёт первый рабочий источник и кеширует ответ на CDN. Для новых товаров в админке можно указать любую ссылку на фото.

## Демо на Vercel
База SQLite из репозитория копируется в `/tmp` при холодном старте, поэтому изменения в админке и заявки на демо со временем сбрасываются (заявки при этом уже ушли в Telegram). Для боевого запуска подключите постоянную базу (Postgres/MySQL через `DB_URL`) или перенесите сайт на обычный хостинг.
