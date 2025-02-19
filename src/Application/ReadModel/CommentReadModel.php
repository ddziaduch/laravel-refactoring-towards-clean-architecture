<?php

namespace Clean\Application\ReadModel;

use DateTime;

final class CommentReadModel
{
    public int $id;
    public DateTime $createdAt;
    public DateTime $updatedAt;
    public string $body;
    public ProfileReadModel $author;

    public function __construct(
        int $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        string $body,
        ProfileReadModel $author
    ) {
        $this->author = $author;
        $this->body = $body;
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
        $this->id = $id;
    }
}
