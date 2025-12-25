<?php

// Загружаем .env файл через Dotenv (если он существует)
$dotenvPath = __DIR__ . '/../';
if (file_exists($dotenvPath . '.env')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
        $dotenv->load();
    } catch (Exception $e) {
        // Игнорируем ошибки загрузки .env
    }
}

// Функция для получения переменной окружения
function env($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return $value !== false ? $value : $default;
}

return [
    'telegram' => [
        // Можно указать в .env файле как TELEGRAM_BOT_TOKEN=ваш_токен
        'bot_token' => env('TELEGRAM_BOT_TOKEN', 'YOUR_BOT_TOKEN_HERE'),
        // Можно указать в .env файле как TELEGRAM_BOT_USERNAME=ваш_username
        'bot_username' => env('TELEGRAM_BOT_USERNAME', 'YOUR_BOT_USERNAME'),
    ],
    'app' => [
        'base_url' => env('APP_URL', 'http://localhost'),
        'session_lifetime' => 43200, // 12 часов
    ],
    'game' => [
        'board_size' => 3,
        'player_symbol' => 'X',
        'computer_symbol' => 'O',
    ],
    'promo' => [
        'code_length' => 5,
    ],
];

