<?php

declare(strict_types=1);

namespace Clean\Domain\Entity;

final class Article
{
    private string $slug;
    private int $authorId;
    private bool $removed = false;

    public function __construct(
        string $slug,
        int $authorId
    ) {
        $this->authorId = $authorId;
        $this->slug = $slug;
    }

    public function wasCreatedBy(int $authorId): bool
    {
        return $this->authorId === $authorId;
    }

    public function markAsRemoved(): void
    {
        $this->removed = true;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function slug(): string
    {
        return $this->slug;
    }
}
