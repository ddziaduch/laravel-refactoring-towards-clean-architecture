<?php

declare(strict_types=1);

namespace Clean\Domain\Entity;

final class Article
{
    /**
     * @var string[]
     */
    private array $tagList;

    public function __construct(
        // should be immutable, smth like id, right?
        public readonly string $slug,
        private string $title,
        private string $description,
        private string $body,
        // I guess it should be immutable
        public readonly int $authorId,
        string ...$tagList
    ) {
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
}
