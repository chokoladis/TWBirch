<?php

namespace App\Repositories;

use App\Core\Session;
use App\Models\Game;

class GameRepository
{
    private const SESSION_KEY = 'current_game';
    
    public function getCurrentGame(): ?Game
    {
        $gameData = Session::get(self::SESSION_KEY);
        if ($gameData === null) {
            return null;
        }
        
        return Game::fromArray($gameData);
    }
    
    public function saveGame(Game $game): void
    {
        Session::set(self::SESSION_KEY, $game->toArray());
    }
    
    public function deleteGame(): void
    {
        Session::remove(self::SESSION_KEY);
    }
}

