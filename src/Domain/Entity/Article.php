<?php

declare(strict_types=1);

namespace Clean\Domain\Entity;

final class Article
{
    private ?int $id;
    private string $slug;
    private int $authorId;
    /**
     * @var string[]
     */
    private array $tagList;
    private string $title;
    private string $description;
    private string $body;

    public function __construct(
        string $slug,
        string $title,
        string $description,
        string $body,
        int $authorId,
        string ...$tagList
    ) {
        $this->authorId = $authorId;
        $this->body = $body;
        $this->description = $description;
        $this->title = $title;
        $this->slug = $slug;
        $this->tagList = $tagList;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function body(): string
    {
        return $this->body;
    }

    /**
     * @return string[]
     */
    public function tagList(): array
    {
        return $this->tagList;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function authorId(): int
    {
        return $this->authorId;
    }

    public function id(): ?int
    {
        return $this->id;
    }
}
