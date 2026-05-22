<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$config = appConfig();
if (session_status() === PHP_SESSION_NONE) {
    session_name((string) $config['session_name']);
    session_start();
}

function csrfToken(): string
{
    if (!isset($_SESSION['csrf_token'], $_SESSION['csrf_created']) || (time() - $_SESSION['csrf_created']) > appConfig()['csrf_ttl']) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_created'] = time();
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function requireAuth(string $role = 'user'): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: /login.php');
        exit;
    }
    if ($role === 'admin' && ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
        http_response_code(403);
        exit('Forbidden');
    }
}

function rateLimit(string $key, int $limit, int $window): bool
{
    $_SESSION['rate_limit'][$key] ??= [];
    $_SESSION['rate_limit'][$key] = array_filter($_SESSION['rate_limit'][$key], fn ($ts) => (time() - $ts) < $window);
    if (count($_SESSION['rate_limit'][$key]) >= $limit) {
        return false;
    }
    $_SESSION['rate_limit'][$key][] = time();
    return true;
}
