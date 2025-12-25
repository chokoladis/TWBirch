<?php

namespace App\Services;

use App\Core\Config;

class TelegramService
{
    private string $botToken;
    private string $apiUrl;
    
    public function __construct()
    {
        $config = Config::get('telegram');
        $this->botToken = $config['bot_token'];
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }
    
    public function sendMessage(int $chatId, string $message): bool
    {
        $url = "{$this->apiUrl}/sendMessage";
        
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            error_log("Telegram API error: {$response}");
            return false;
        }
        
        $result = json_decode($response, true);
        return isset($result['ok']) && $result['ok'] === true;
    }
    
    public function validateAuth(array $authData): bool
    {
        if (!isset($authData['hash'])) {
            return false;
        }
        
        $checkHash = $authData['hash'];
        $authDataCopy = $authData;
        unset($authDataCopy['hash']);
        
        $dataCheckArr = [];
        foreach ($authDataCopy as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }
        sort($dataCheckArr);
        $dataCheckString = implode("\n", $dataCheckArr);
        $secretKey = hash('sha256', $this->botToken, true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);
        
        return hash_equals($hash, $checkHash);
    }
}

