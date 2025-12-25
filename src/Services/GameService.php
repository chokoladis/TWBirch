<?php

namespace App\Services;

use App\Models\Game;

class GameService
{
    private const WINNING_COMBINATIONS = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // горизонтали
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // вертикали
        [0, 4, 8], [2, 4, 6], // диагонали
    ];
    
    private const PLAYER_SYMBOL = 'X';
    private const COMPUTER_SYMBOL = 'O';
    
    public function makePlayerMove(Game $game, int $position): bool
    {
        if ($game->isFinished() || $game->getCurrentPlayer() !== self::PLAYER_SYMBOL) {
            return false;
        }
        
        $board = $game->getBoard();
        if ($board[$position] !== '') {
            return false;
        }
        
        $board[$position] = self::PLAYER_SYMBOL;
        $game->setBoard($board);
        
        $winner = $this->checkWinner($board);
        if ($winner) {
            $game->setFinished(true);
            $game->setWinner($winner);
            if ($winner === self::PLAYER_SYMBOL) {
                $game->setPlayerScore($game->getPlayerScore() + 1);
            }
            return true;
        }
        
        if ($this->isBoardFull($board)) {
            $game->setFinished(true);
            $game->setWinner('tie');
            return true;
        }
        
        $game->setCurrentPlayer(self::COMPUTER_SYMBOL);
        return true;
    }
    
    public function makeComputerMove(Game $game): ?int
    {
        if ($game->isFinished() || $game->getCurrentPlayer() !== self::COMPUTER_SYMBOL) {
            return null;
        }
        
        $board = $game->getBoard();
        $position = $this->findBestMove($board);
        
        if ($position === null) {
            return null;
        }
        
        $board[$position] = self::COMPUTER_SYMBOL;
        $game->setBoard($board);
        
        $winner = $this->checkWinner($board);
        if ($winner) {
            $game->setFinished(true);
            $game->setWinner($winner);
            if ($winner === self::COMPUTER_SYMBOL) {
                $game->setComputerScore($game->getComputerScore() + 1);
            }
        } elseif ($this->isBoardFull($board)) {
            $game->setFinished(true);
            $game->setWinner('tie');
        } else {
            $game->setCurrentPlayer(self::PLAYER_SYMBOL);
        }
        
        return $position;
    }
    
    private function findBestMove(array $board): ?int
    {
        // 1. Проверяем, можем ли выиграть
        for ($i = 0; $i < 9; $i++) {
            if ($board[$i] === '') {
                $board[$i] = self::COMPUTER_SYMBOL;
                if ($this->checkWinner($board) === self::COMPUTER_SYMBOL) {
                    return $i;
                }
                $board[$i] = '';
            }
        }
        
        // 2. Блокируем ход игрока
        for ($i = 0; $i < 9; $i++) {
            if ($board[$i] === '') {
                $board[$i] = self::PLAYER_SYMBOL;
                if ($this->checkWinner($board) === self::PLAYER_SYMBOL) {
                    $board[$i] = '';
                    return $i;
                }
                $board[$i] = '';
            }
        }
        
        // 3. Занимаем центр, если свободен
        if ($board[4] === '') {
            return 4;
        }
        
        // 4. Случайный ход из доступных
        $availableMoves = [];
        for ($i = 0; $i < 9; $i++) {
            if ($board[$i] === '') {
                $availableMoves[] = $i;
            }
        }
        
        if (empty($availableMoves)) {
            return null;
        }
        
        return $availableMoves[array_rand($availableMoves)];
    }
    
    private function checkWinner(array $board): ?string
    {
        foreach (self::WINNING_COMBINATIONS as $combination) {
            [$a, $b, $c] = $combination;
            if ($board[$a] !== '' && 
                $board[$a] === $board[$b] && 
                $board[$a] === $board[$c]) {
                return $board[$a];
            }
        }
        
        return null;
    }
    
    private function isBoardFull(array $board): bool
    {
        return !in_array('', $board, true);
    }
    
    public function resetGame(Game $game): void
    {
        $game->setBoard(array_fill(0, 9, ''));
        $game->setCurrentPlayer(self::PLAYER_SYMBOL);
        $game->setFinished(false);
        $game->setWinner(null);
    }
}

