<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Web Routes
 * ------------------------------------------------------------
 * Maps all public and admin URLs to controllers
 * ------------------------------------------------------------
 */

use App\Core\Router;
use App\Controllers\{
    HomeController,
    ServiceController,
    BlogController,
    ContactController,
    AuthController,
    AccountController,
    MediaController,
    PaymentController,
    SettingsController,
    BlogAdminController
};

// ========== FRONTEND ROUTES ==========

// Home & Static Pages
Router::get('/', [HomeController::class, 'index']);
Router::get('/privacy-policy', fn() => view('privacy-policy'));
Router::get('/terms', fn() => view('terms'));
Router::get('/thank-you', fn() => view('thank-you'));

// Services
Router::get('/services', [ServiceController::class, 'list']);
Router::get('/service/{slug}', [ServiceController::class, 'detail']);
Router::post('/service/{slug}/quote', [ServiceController::class, 'quote']);

// Portfolio
Router::get('/portfolio', [HomeController::class, 'portfolio']);

// Contact
Router::get('/contact', [ContactController::class, 'index']);
Router::post('/contact/submit', [ContactController::class, 'submit']);

// Blog
Router::get('/blog', [BlogController::class, 'index']);
Router::get('/blog/{slug}', [BlogController::class, 'show']);
Router::get('/blog/{slug}/amp', [BlogController::class, 'amp']);

// Authentication
Router::get('/login', [AuthController::class, 'login']);
Router::post('/login', [AuthController::class, 'loginPost']);
Router::get('/register', [AuthController::class, 'register']);
Router::post('/register', [AuthController::class, 'registerPost']);
Router::get('/verify-2fa', [AuthController::class, 'verify2FA']);
Router::post('/verify-2fa', [AuthController::class, 'verify2FA']);
Router::get('/logout', [AuthController::class, 'logout']);

// Payments
Router::get('/pay/{order_id}', [PaymentController::class, 'pay']);
Router::get('/payment-success', [PaymentController::class, 'success']);
Router::post('/payment/webhook', [PaymentController::class, 'webhook']);

// ========== CLIENT ACCOUNT ROUTES ==========
Router::group('/account', function () {
    Router::get('/', [AccountController::class, 'dashboard']);
    Router::get('/project/{id}', [AccountController::class, 'project']);
    Router::get('/messages', [AccountController::class, 'messages']);
    Router::get('/messages/{id}', [AccountController::class, 'projectMessages']);
    Router::get('/profile', [AccountController::class, 'profile']);
    Router::post('/profile/update', [AccountController::class, 'updateProfile']);
    Router::post('/project/{id}/message', [AccountController::class, 'sendMessage']);
}, ['middleware' => 'auth']);

// ========== ADMIN PANEL ROUTES ==========
Router::group('/admin', function () {
    Router::get('/', fn() => redirect('/admin/dashboard'));
    Router::get('/dashboard', [\App\Controllers\AdminController::class, 'dashboard']);

    // Services
    Router::get('/services', [\App\Controllers\AdminServicesController::class, 'index']);
    Router::post('/services/save', [\App\Controllers\AdminServicesController::class, 'save']);
    Router::get('/services/get', [\App\Controllers\AdminServicesController::class, 'get']);
    Router::post('/services/delete', [\App\Controllers\AdminServicesController::class, 'delete']);

    // Leads / Quotes
    Router::get('/leads', [\App\Controllers\AdminLeadsController::class, 'index']);
    Router::post('/leads/mark', [\App\Controllers\AdminLeadsController::class, 'mark']);
    Router::post('/leads/delete', [\App\Controllers\AdminLeadsController::class, 'delete']);
    Router::get('/leads/export', [\App\Controllers\AdminLeadsController::class, 'export']);

    // Media Library
    Router::get('/media', [MediaController::class, 'index']);
    Router::post('/media/upload', [MediaController::class, 'upload']);
    Router::post('/media/delete', [MediaController::class, 'delete']);

    // Blog CMS
    Router::get('/blogs', [BlogAdminController::class, 'list']);
    Router::get('/blogs/create', [BlogAdminController::class, 'create']);
    Router::post('/blogs/create', [BlogAdminController::class, 'createPost']);
    Router::get('/blogs/edit/{id}', [BlogAdminController::class, 'edit']);
    Router::post('/blogs/edit/{id}', [BlogAdminController::class, 'editPost']);
    Router::post('/blogs/delete/{id}', [BlogAdminController::class, 'delete']);

    // Settings
    Router::get('/settings', [SettingsController::class, 'index']);
    Router::post('/settings/save', [SettingsController::class, 'save']);

    // Backups
    Router::get('/backups', [\App\Controllers\BackupController::class, 'index']);
    Router::post('/backups/create', [\App\Controllers\BackupController::class, 'create']);
    Router::post('/backups/delete', [\App\Controllers\BackupController::class, 'delete']);
}, ['middleware' => 'admin']);

// ========== ERROR ROUTES ==========
Router::fallback(function () {
    http_response_code(404);
    view('errors/404');
});
