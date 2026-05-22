<?php

declare(strict_types=1);

function env(string $key, ?string $default = null): ?string
{
    static $loaded = false;
    static $vars = [];

    if (!$loaded) {
        $path = dirname(__DIR__) . '/.env';
        if (is_file($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                $vars[trim($k)] = trim($v);
            }
        }
        $loaded = true;
    }

    return $_ENV[$key] ?? $_SERVER[$key] ?? $vars[$key] ?? $default;
}

function appConfig(): array
{
    return [
        'db' => [
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => (int) env('DB_PORT', '3306'),
            'name' => env('DB_NAME', 'streaming_service'),
            'user' => env('DB_USER', 'root'),
            'pass' => env('DB_PASS', ''),
        ],
        'session_name' => env('SESSION_NAME', 'streaming_sess'),
        'csrf_ttl' => (int) env('CSRF_TOKEN_TTL', '7200'),
        'jellyfin' => [
            'url' => rtrim((string) env('JELLYFIN_URL', ''), '/'),
            'api_key' => (string) env('JELLYFIN_API_KEY', ''),
            'device_id' => (string) env('JELLYFIN_DEVICE_ID', 'streaming-service'),
        ],
        'queue_max_retries' => (int) env('QUEUE_MAX_RETRIES', '5'),
        'rate_limit' => [
            'login' => ['limit' => (int) env('LOGIN_RATE_LIMIT', '5'), 'window' => (int) env('LOGIN_RATE_WINDOW', '300')],
            'signup' => ['limit' => (int) env('SIGNUP_RATE_LIMIT', '3'), 'window' => (int) env('SIGNUP_RATE_WINDOW', '600')],
        ],
    ];
}
