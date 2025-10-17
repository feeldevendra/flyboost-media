<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Environment Variable Loader
 * ------------------------------------------------------------
 * Securely loads environment variables from /app/.env
 * for API keys, secrets, and third-party credentials.
 * ------------------------------------------------------------
 */

namespace App;

class Env
{
    private static ?array $vars = null;

    /**
     * Load and cache environment variables.
     */
    private static function load(): void
    {
        if (self::$vars !== null) {
            return;
        }

        $envPath = APP_PATH . '/.env';
        self::$vars = [];

        if (!file_exists($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                self::$vars[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
            }
        }
    }

    /**
     * Get an environment variable.
     */
    public static function get(string $key, $default = null)
    {
        self::load();
        return self::$vars[$key] ?? $default;
    }

    /**
     * Set or override a value (useful for runtime testing)
     */
    public static function set(string $key, $value): void
    {
        self::$vars[$key] = $value;
    }

    /**
     * Return all environment values
     */
    public static function all(): array
    {
        self::load();
        return self::$vars;
    }
}
