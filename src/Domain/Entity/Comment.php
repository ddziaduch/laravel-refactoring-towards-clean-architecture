<?php

namespace Clean\Domain\Entity;

class Comment
{
    private ?int $id = null;

    public function __construct(
        public readonly int $articleId,
        private string $body,
        public readonly int $authorId
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function body(): string
    {
        return $this->body;
    }
}
