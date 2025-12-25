<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    
    public function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }
    
    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                call_user_func($route['handler']);
                return;
            }
        }
        
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
    
    private function matchPath(string $routePath, string $requestPath): bool
    {
        $routePath = rtrim($routePath, '/');
        $requestPath = rtrim($requestPath, '/');
        
        if ($routePath === $requestPath) {
            return true;
        }
        
        // Простой паттерн для параметров
        $pattern = preg_replace('/\{[^}]+\}/', '[^/]+', $routePath);
        return preg_match('#^' . $pattern . '$#', $requestPath);
    }
}

