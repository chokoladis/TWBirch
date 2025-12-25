<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Models\Game;
use App\Repositories\GameRepository;
use App\Repositories\UserRepository;
use App\Services\GameService;
use App\Services\PromoCodeService;
use App\Services\TelegramService;

class GameController
{
    private GameService $gameService;
    private GameRepository $gameRepository;
    private UserRepository $userRepository;
    private TelegramService $telegramService;
    private PromoCodeService $promoCodeService;
    
    public function __construct()
    {
        $this->gameService = new GameService();
        $this->gameRepository = new GameRepository();
        $this->userRepository = new UserRepository();
        $this->telegramService = new TelegramService();
        $this->promoCodeService = new PromoCodeService();
    }
    
    public function makeMove(): void
    {
        $user = $this->userRepository->getCurrentUser();
        if ($user === null) {
            JsonResponse::error('Необходима авторизация', 401);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['position']) || !is_numeric($input['position'])) {
            JsonResponse::error('Неверная позиция', 400);
            return;
        }
        
        $position = (int) $input['position'];
        if ($position < 0 || $position > 8) {
            JsonResponse::error('Позиция вне диапазона', 400);
            return;
        }
        
        $game = $this->gameRepository->getCurrentGame();
        if ($game === null) {
            $game = new Game();
        }
        
        if (!$this->gameService->makePlayerMove($game, $position)) {
            JsonResponse::error('Невозможно сделать ход', 400);
            return;
        }
        
        $promoCode = null;
        $message = null;
        
        // Если игра закончена победой игрока
        if ($game->isFinished() && $game->getWinner() === 'X') {
            $promoCode = $this->promoCodeService->generate();
            $message = "Победа! Промокод выдан: {$promoCode}";
            $this->telegramService->sendMessage($user->getChatId(), $message);
        }
        
        // Ход компьютера, если игра не закончена
        $computerPosition = null;
        if (!$game->isFinished()) {
            $computerPosition = $this->gameService->makeComputerMove($game);
            
            // Если компьютер выиграл
            if ($game->isFinished() && $game->getWinner() === 'O') {
                $this->telegramService->sendMessage($user->getChatId(), 'Проигрыш');
            }
        }
        
        $this->gameRepository->saveGame($game);
        
        JsonResponse::success([
            'game' => $game->toArray(),
            'computer_position' => $computerPosition,
            'promo_code' => $promoCode,
        ]);
    }
    
    public function getState(): void
    {
        $user = $this->userRepository->getCurrentUser();
        if ($user === null) {
            JsonResponse::error('Необходима авторизация', 401);
            return;
        }
        
        $game = $this->gameRepository->getCurrentGame();
        if ($game === null) {
            $game = new Game();
            $this->gameRepository->saveGame($game);
        }
        
        JsonResponse::success(['game' => $game->toArray()]);
    }
    
    public function reset(): void
    {
        $user = $this->userRepository->getCurrentUser();
        if ($user === null) {
            JsonResponse::error('Необходима авторизация', 401);
            return;
        }
        
        $game = $this->gameRepository->getCurrentGame();
        if ($game === null) {
            $game = new Game();
        }
        
        $this->gameService->resetGame($game);
        $this->gameRepository->saveGame($game);
        
        JsonResponse::success(['game' => $game->toArray()]);
    }
}

