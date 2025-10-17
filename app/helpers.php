<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Global Helper Functions
 * ------------------------------------------------------------
 * Utility functions used across controllers, views, and models.
 * ------------------------------------------------------------
 */

namespace App;

use App\Models\Setting;

/**
 * Fetch a setting value by key
 */
function setting(string $key, $default = null)
{
    return Setting::get($key, $default);
}

/**
 * Render a view file within the main layout
 */
function view(string $path, array $data = []): void
{
    extract($data);
    $viewPath = APP_PATH . '/views/' . $path . '.php';
    include APP_PATH . '/views/layouts/main.php';
}

/**
 * Redirect to another page
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Escape output safely for HTML
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if a user is logged in
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Return the current logged-in user array
 */
function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Check if current user has a given role
 */
function hasRole(string $role): bool
{
    $u = user();
    return $u && strtoupper($u['role']) === strtoupper($role);
}

/**
 * Generate CSRF token for form security
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/**
 * Verify CSRF token from POST submission
 */
function csrf_verify(string $token): bool
{
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

/**
 * Log user actions (admin activities, edits, etc.)
 */
function log_action(string $action, string $details = ''): void
{
    $file = BASE_PATH . '/storage/activity.log';
    $entry = "[" . date('Y-m-d H:i:s') . "] " .
             ($user = user() ? ($user['email'] . ' ') : '') .
             strtoupper($action) . ': ' . $details . PHP_EOL;

    if (!is_dir(dirname($file))) {
        mkdir(dirname($file), 0775, true);
    }

    file_put_contents($file, $entry, FILE_APPEND);
}

/**
 * Return the base URL for links
 */
function base_url(string $path = ''): string
{
    return rtrim(Config::BASE_URL . '/' . ltrim($path, '/'), '/');
}
