<?php

namespace App\Services;

use App\Core\Config;

class PromoCodeService
{
    private int $codeLength;
    
    public function __construct()
    {
        $config = Config::get('promo');
        $this->codeLength = $config['code_length'] ?? 5;
    }
    
    public function generate(): string
    {
        $min = pow(10, $this->codeLength - 1);
        $max = pow(10, $this->codeLength) - 1;
        return (string) random_int($min, $max);
    }
    
    public function validate(string $code): bool
    {
        return preg_match('/^\d{' . $this->codeLength . '}$/', $code) === 1;
    }
}

