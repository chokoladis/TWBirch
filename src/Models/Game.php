<?php

namespace App\Models;

class Game
{
    private array $board;
    private string $currentPlayer;
    private bool $isFinished;
    private ?string $winner;
    private int $playerScore;
    private int $computerScore;
    
    public function __construct()
    {
        $this->board = array_fill(0, 9, '');
        $this->currentPlayer = 'X';
        $this->isFinished = false;
        $this->winner = null;
        $this->playerScore = 0;
        $this->computerScore = 0;
    }
    
    public function getBoard(): array
    {
        return $this->board;
    }
    
    public function setBoard(array $board): void
    {
        $this->board = $board;
    }
    
    public function getCurrentPlayer(): string
    {
        return $this->currentPlayer;
    }
    
    public function setCurrentPlayer(string $player): void
    {
        $this->currentPlayer = $player;
    }
    
    public function isFinished(): bool
    {
        return $this->isFinished;
    }
    
    public function setFinished(bool $finished): void
    {
        $this->isFinished = $finished;
    }
    
    public function getWinner(): ?string
    {
        return $this->winner;
    }
    
    public function setWinner(?string $winner): void
    {
        $this->winner = $winner;
    }
    
    public function getPlayerScore(): int
    {
        return $this->playerScore;
    }
    
    public function setPlayerScore(int $score): void
    {
        $this->playerScore = $score;
    }
    
    public function getComputerScore(): int
    {
        return $this->computerScore;
    }
    
    public function setComputerScore(int $score): void
    {
        $this->computerScore = $score;
    }
    
    public function toArray(): array
    {
        return [
            'board' => $this->board,
            'current_player' => $this->currentPlayer,
            'is_finished' => $this->isFinished,
            'winner' => $this->winner,
            'player_score' => $this->playerScore,
            'computer_score' => $this->computerScore,
        ];
    }
    
    public static function fromArray(array $data): self
    {
        $game = new self();
        $game->setBoard($data['board'] ?? []);
        $game->setCurrentPlayer($data['current_player'] ?? 'X');
        $game->setFinished($data['is_finished'] ?? false);
        $game->setWinner($data['winner'] ?? null);
        $game->setPlayerScore($data['player_score'] ?? 0);
        $game->setComputerScore($data['computer_score'] ?? 0);
        return $game;
    }
}

