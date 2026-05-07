<?php

namespace App\Models;

class Review
{
    private ?int $id;
    private int $userId;
    private int $titleId;
    private float $score;
    private ?string $body;
    private bool $isFlagged;
    private bool $isVisible;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        int $userId,
        int $titleId,
        float $score,
        ?string $body = null,
        bool $isFlagged = false,
        bool $isVisible = true,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->titleId = $titleId;
        $this->score = $score;
        $this->body = $body;
        $this->isFlagged = $isFlagged;
        $this->isVisible = $isVisible;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitleId(): int
    {
        return $this->titleId;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function isFlagged(): bool
    {
        return $this->isFlagged;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setTitleId(int $titleId): void
    {
        $this->titleId = $titleId;
    }

    public function setScore(float $score): void
    {
        $this->score = $score;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function setIsFlagged(bool $isFlagged): void
    {
        $this->isFlagged = $isFlagged;
    }

    public function setIsVisible(bool $isVisible): void
    {
        $this->isVisible = $isVisible;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hide(): void
    {
        $this->isVisible = false;
    }

    public function show(): void
    {
        $this->isVisible = true;
    }

    public function flag(): void
    {
        $this->isFlagged = true;
    }

    public function unflag(): void
    {
        $this->isFlagged = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Mapping
    |--------------------------------------------------------------------------
    */

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) $data['user_id'],
            (int) $data['title_id'],
            (float) $data['score'],
            $data['body'] ?? null,
            isset($data['is_flagged'])
                ? (bool) $data['is_flagged']
                : false,
            isset($data['is_visible'])
                ? (bool) $data['is_visible']
                : true,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'title_id' => $this->titleId,
            'score' => $this->score,
            'body' => $this->body,
            'is_flagged' => $this->isFlagged,
            'is_visible' => $this->isVisible,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}