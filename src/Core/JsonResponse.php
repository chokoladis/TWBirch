<?php

namespace App\Core;

class JsonResponse
{
    public static function success($data = null, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    public static function error(string $message, int $code = 400, $errors = null): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => $message,
            'errors' => $errors,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

