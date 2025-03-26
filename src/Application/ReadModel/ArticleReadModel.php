<?php

declare(strict_types=1);

namespace Clean\Application\ReadModel;

final class ArticleReadModel
{
    public int $id;
    public string $slug;
    public string $title;
    public string $description;
    public string $body;
    /**
     * @var string[]
     */
    public array $tagList;
    public \DateTime $createdAt;
    public \DateTime $updatedAt;
    public int $favoritesCount;
    public ProfileReadModel $author;
    public bool $favorited;

    /**
     * @param string[] $tagList
     */
    public function __construct(
        int $id,
        string $slug,
        string $title,
        string $description,
        string $body,
        array $tagList,
        \DateTime $createdAt,
        \DateTime $updatedAt,
        int $favoritesCount,
        bool $favorited,
        ProfileReadModel $author
    ) {
        $this->author = $author;
        $this->favorited = $favorited;
        $this->favoritesCount = $favoritesCount;
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
        $this->tagList = $tagList;
        $this->body = $body;
        $this->description = $description;
        $this->title = $title;
        $this->slug = $slug;
        $this->id = $id;
    }
}
