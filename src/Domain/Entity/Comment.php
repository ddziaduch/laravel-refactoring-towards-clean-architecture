<?php

namespace Clean\Domain\Entity;

class Comment
{
    private string $articleSlug;
    private int $authorId;
    private ?int $id = null;
    private string $body;

    public function __construct(
        string $articleSlug,
        string $body,
        int $authorId
    ) {
        $this->authorId = $authorId;
        $this->body = $body;
        $this->articleSlug = $articleSlug;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function articleSlug(): string
    {
        return $this->articleSlug;
    }

    public function authorId(): int
    {
        return $this->authorId;
    }
}
