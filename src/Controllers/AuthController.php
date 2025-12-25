<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\TelegramService;

class AuthController
{
    private TelegramService $telegramService;
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->telegramService = new TelegramService();
        $this->userRepository = new UserRepository();
    }
    
    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id'], $input['first_name'], $input['auth_date'], $input['hash'])) {
            JsonResponse::error('Недостаточно данных для авторизации', 400);
            return;
        }
        
        if (!$this->telegramService->validateAuth($input)) {
            JsonResponse::error('Неверная подпись авторизации', 401);
            return;
        }
        
        $user = new User(
            $input['id'],
            $input['first_name'],
            $input['username'] ?? null,
            $input['id'] // chat_id = user_id
        );
        
        $this->userRepository->saveUser($user);
        
        JsonResponse::success(['user' => $user->toArray()]);
    }
    
    public function logout(): void
    {
        $this->userRepository->deleteUser();
        JsonResponse::success(['message' => 'Выход выполнен']);
    }
    
    public function check(): void
    {
        $user = $this->userRepository->getCurrentUser();
        
        if ($user === null) {
            JsonResponse::success(['authenticated' => false]);
            return;
        }
        
        JsonResponse::success([
            'authenticated' => true,
            'user' => $user->toArray(),
        ]);
    }
}

