<?php

namespace App\Repositories;

use App\Core\Session;
use App\Models\User;

class UserRepository
{
    private const SESSION_KEY = 'telegram_user';
    
    public function getCurrentUser(): ?User
    {
        $userData = Session::get(self::SESSION_KEY);
        if ($userData === null) {
            return null;
        }
        
        return User::fromArray($userData);
    }
    
    public function saveUser(User $user): void
    {
        Session::set(self::SESSION_KEY, $user->toArray());
    }
    
    public function deleteUser(): void
    {
        Session::remove(self::SESSION_KEY);
    }
}

