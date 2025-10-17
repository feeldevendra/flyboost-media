<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Router System
 * ------------------------------------------------------------
 * Handles route registration, pattern matching,
 * controller dispatch, and access control.
 * ------------------------------------------------------------
 */

namespace App;

class Router
{
    private array $routes = [];

    /**
     * Register a GET route
     */
    public function get(string $pattern, array $action, bool $auth = false, ?string $role = null): void
    {
        $this->routes['GET'][] = compact('pattern', 'action', 'auth', 'role');
    }

    /**
     * Register a POST route
     */
    public function post(string $pattern, array $action, bool $auth = false, ?string $role = null): void
    {
        $this->routes['POST'][] = compact('pattern', 'action', 'auth', 'role');
    }

    /**
     * Dispatch the incoming request
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = Config::BASE_URL;

        // Remove base path if app in subfolder
        if ($base && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri = '/' . trim($uri, '/');

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            $pattern = preg_replace('#\{([^/]+)\}#', '([^/]+)', $route['pattern']);
            $pattern = '#^' . rtrim($pattern, '/') . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // remove full match

                // Auth check
                if ($route['auth'] && !isLoggedIn()) {
                    redirect('/login');
                }

                // Role-based access
                if ($route['role'] && (!user() || strtoupper(user()['role']) !== strtoupper($route['role']))) {
                    redirect('/');
                }

                [$controllerName, $methodName] = $route['action'];

                if (!class_exists($controllerName)) {
                    die("Controller not found: $controllerName");
                }

                $controller = new $controllerName();

                if (!method_exists($controller, $methodName)) {
                    die("Method not found: {$controllerName}::{$methodName}");
                }

                // Call controller method with params
                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }

        // No match found → 404
        http_response_code(404);
        include APP_PATH . '/views/errors/404.php';
    }
}
