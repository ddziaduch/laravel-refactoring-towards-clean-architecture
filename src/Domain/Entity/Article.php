<?php

declare(strict_types=1);

namespace Clean\Domain\Entity;

final class Article
{
    private bool $removed = false;

    public function __construct(
        public readonly string $slug,
        public readonly int $authorId,
    ) {
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
}
