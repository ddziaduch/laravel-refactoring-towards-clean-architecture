<?php

declare(strict_types=1);

namespace Clean\Domain\Entity;

final class Comment
{
    private ?int $id;
    private string $articleSlug;
    private int $authorId;
    private string $body;

    private function __construct(
        ?int $id,
        string $articleSlug,
        int $authorId,
        string $commentBody
    ) {
        $this->id = $id;
        $this->articleSlug = $articleSlug;
        $this->authorId = $authorId;
        $this->body = $commentBody;
    }

    public static function new(
        string $articleSlug,
        int $authorId,
        string $commentBody
    ): self {
        return new self(
            null,
            $articleSlug,
            $authorId,
            $commentBody
        );
    }

    public function withId(int $id): self
    {
        return new Comment($id, $this->articleSlug, $this->authorId, $this->body);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArticleSlug(): string
    {
        return $this->articleSlug;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
