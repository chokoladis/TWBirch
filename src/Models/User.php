<?php

namespace App\Models;

class User
{
    private int $id;
    private string $firstName;
    private ?string $username;
    private int $chatId;
    
    public function __construct(int $id, string $firstName, ?string $username, int $chatId)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->username = $username;
        $this->chatId = $chatId;
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    
    public function getUsername(): ?string
    {
        return $this->username;
    }
    
    public function getChatId(): int
    {
        return $this->chatId;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'username' => $this->username,
            'chat_id' => $this->chatId,
        ];
    }
    
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['first_name'],
            $data['username'] ?? null,
            $data['chat_id'] ?? $data['id']
        );
    }
}

