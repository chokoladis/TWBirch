<?php

namespace App\Core;

class Config
{
    private static ?array $config = null;
    
    public static function load(): void
    {
        if (self::$config === null) {
            $configPath = __DIR__ . '/../../config/config.php';
            if (file_exists($configPath)) {
                self::$config = require $configPath;
            } else {
                self::$config = [];
            }
        }
    }
    
    public static function get(string $key, $default = null)
    {
        self::load();
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

