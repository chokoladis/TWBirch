<?php

// Загружаем Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\GameController;

Session::start();

$router = new Router();

// Auth routes
$router->addRoute('POST', '/api/auth/login', function() {
    (new AuthController())->login();
});

$router->addRoute('POST', '/api/auth/logout', function() {
    (new AuthController())->logout();
});

$router->addRoute('GET', '/api/auth/check', function() {
    (new AuthController())->check();
});

// Game routes
$router->addRoute('POST', '/api/game/move', function() {
    (new GameController())->makeMove();
});

$router->addRoute('GET', '/api/game/state', function() {
    (new GameController())->getState();
});

$router->addRoute('POST', '/api/game/reset', function() {
    (new GameController())->reset();
});

// Frontend route
$router->addRoute('GET', '/', function() {
    require __DIR__ . '/../views/index.php';
});

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($method, $path);

