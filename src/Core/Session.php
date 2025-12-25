<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}

