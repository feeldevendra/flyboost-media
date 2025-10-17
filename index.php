<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Main Entry Point
 * ------------------------------------------------------------
 * Environment bootstrap, autoloading, routing, and error handling.
 * ------------------------------------------------------------
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('VIEW_PATH', APP_PATH . '/views');

require_once APP_PATH . '/core/autoload.php';
require_once APP_PATH . '/core/helpers.php';
require_once APP_PATH . '/core/Router.php';

// Load environment variables (.env)
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = array_map('trim', explode('=', $line, 2));
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}

// Connect to Database
require_once APP_PATH . '/core/Database.php';
\App\DB::init([
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'dbname' => $_ENV['DB_NAME'] ?? 'flyboost_media',
    'user' => $_ENV['DB_USER'] ?? 'root',
    'pass' => $_ENV['DB_PASS'] ?? ''
]);

// Include Routes
require_once APP_PATH . '/routes/web.php';

// Dispatch Route
use App\Core\Router;

try {
    Router::dispatch();
} catch (Throwable $e) {
    error_log("[ERROR] " . $e->getMessage());
    http_response_code(500);
    view('errors/500', ['message' => $e->getMessage()]);
}
