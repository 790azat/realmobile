<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Вход в админку · {{ config('shop.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700;800&display=swap&subset=cyrillic" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-paper p-4">
    <form method="POST" action="{{ route('admin.login') }}" class="card w-full max-w-sm space-y-4 p-6 shadow-sm">
        @csrf
        <div class="mb-2">@include('partials.logo')</div>
        <div class="text-xl font-bold">Вход в админ-панель</div>
        <div>
            <label class="label" for="login">Логин</label>
            <input id="login" name="login" value="{{ old('login') }}" class="input" autocomplete="username" required>
        </div>
        <div>
            <label class="label" for="password">Пароль</label>
            <input id="password" name="password" type="password" class="input" autocomplete="current-password" required>
        </div>
        @error('login')<div class="text-sm text-brand">{{ $message }}</div>@enderror
        <button class="btn-brand w-full">Войти</button>
    </form>
</body>
</html>
