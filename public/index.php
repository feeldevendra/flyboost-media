<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Public Index (Front Controller)
 * ------------------------------------------------------------
 * - Loads configuration, environment variables, and routing
 * - All requests go through this file (via .htaccess)
 * - Initializes session, autoload, and router dispatch
 * ------------------------------------------------------------
 */

declare(strict_types=1);
session_start();

// Define important base paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Autoload core files
require_once APP_PATH . '/config.php';
require_once APP_PATH . '/env.php';
require_once APP_PATH . '/db.php';
require_once APP_PATH . '/helpers.php';
require_once APP_PATH . '/router.php';

use App\Router;

// Initialize Router
$router = new Router();

/**
 * ------------------------------------------------------------
 * Public Routes
 * ------------------------------------------------------------
 */
$router->get('/', ['App\\Controllers\\HomeController', 'index']);
$router->get('/services', ['App\\Controllers\\ServiceController', 'list']);
$router->get('/service/{slug}', ['App\\Controllers\\ServiceController', 'detail']);
$router->post('/service/{slug}/quote', ['App\\Controllers\\ServiceController', 'quote']);

$router->get('/blog', ['App\\Controllers\\BlogController', 'index']);
$router->get('/blog/{slug}', ['App\\Controllers\\BlogController', 'show']);

$router->get('/contact', ['App\\Controllers\\ContactController', 'index']);
$router->post('/contact', ['App\\Controllers\\ContactController', 'submit']);

$router->get('/login', ['App\\Controllers\\AuthController', 'login']);
$router->post('/login', ['App\\Controllers\\AuthController', 'loginPost']);
$router->get('/register', ['App\\Controllers\\AuthController', 'register']);
$router->post('/register', ['App\\Controllers\\AuthController', 'registerPost']);
$router->get('/logout', ['App\\Controllers\\AuthController', 'logout']);

$router->get('/account', ['App\\Controllers\\AccountController', 'dashboard'], true);

/**
 * ------------------------------------------------------------
 * Payment & Webhooks
 * ------------------------------------------------------------
 */
$router->get('/pay/{order_id}', ['App\\Controllers\\PaymentController', 'pay']);
$router->get('/payment-success', ['App\\Controllers\\PaymentController', 'success']);
$router->post('/payment-webhook', ['App\\Controllers\\PaymentController', 'webhook']);

/**
 * ------------------------------------------------------------
 * Admin Routes
 * ------------------------------------------------------------
 */
$router->get('/admin', ['App\\Controllers\\AdminController', 'dashboard'], true, 'ADMIN');
$router->get('/admin/services', ['App\\Controllers\\AdminController', 'services'], true, 'ADMIN');
$router->get('/admin/settings', ['App\\Controllers\\SettingsController', 'index'], true, 'ADMIN');
$router->post('/admin/settings', ['App\\Controllers\\SettingsController', 'save'], true, 'ADMIN');
$router->get('/admin/media', ['App\\Controllers\\MediaController', 'index'], true, 'ADMIN');
$router->post('/admin/media/upload', ['App\\Controllers\\MediaController', 'upload'], true, 'ADMIN');

$router->get('/admin/blogs', ['App\\Controllers\\BlogAdminController', 'list'], true, 'ADMIN');
$router->get('/admin/blogs/create', ['App\\Controllers\\BlogAdminController', 'create'], true, 'ADMIN');
$router->post('/admin/blogs/create', ['App\\Controllers\\BlogAdminController', 'createPost'], true, 'ADMIN');
$router->get('/admin/blogs/{id}/edit', ['App\\Controllers\\BlogAdminController', 'edit'], true, 'ADMIN');
$router->post('/admin/blogs/{id}/edit', ['App\\Controllers\\BlogAdminController', 'editPost'], true, 'ADMIN');
$router->post('/admin/blogs/{id}/delete', ['App\\Controllers\\BlogAdminController', 'delete'], true, 'ADMIN');

// Dispatch the route
$router->dispatch();
