<?php

declare(strict_types=1);

namespace Clean\Application\ReadModel;

final class CommentReadModel
{
    public UserReadModel $author;
    public \DateTimeInterface $updatedAt;
    public \DateTimeInterface $createdAt;
    public string $body;
    public int $id;

    public function __construct(
        int $id,
        string $body,
        \DateTimeInterface $createdAt,
        \DateTimeInterface $updatedAt,
        UserReadModel $author
    ) {
        $this->id = $id;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->author = $author;
    }
}
