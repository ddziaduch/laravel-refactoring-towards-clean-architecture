<?php

namespace Conduit\Article\Domain;

use Conduit\Article\Domain\Exception\NotArticleAuthor;

final readonly class Article
{
    public function __construct(
        public ?int $id,
        public int $authorId,
        public string $slug,
        public string $title,
        public string $description,
        public string $body,
        public TagList $tags,
    ) {}

    public static function publish(
        int $authorId,
        string $slug,
        string $title,
        string $description,
        string $body,
        TagList $tags,
    ): self {
        return new self(null, $authorId, $slug, $title, $description, $body, $tags);
    }

    /**
     * Only the author may change or remove an article.
     *
     * @throws NotArticleAuthor
     */
    public function assertAuthoredBy(int $userId): void
    {
        if ($userId !== $this->authorId) {
            throw NotArticleAuthor::of($this->slug, $userId);
        }
    }

    /**
     * A new title always comes together with its slug, so they cannot drift apart.
     *
     * @throws NotArticleAuthor
     */
    public function edit(
        int $editorId,
        ?string $title = null,
        ?string $slug = null,
        ?string $description = null,
        ?string $body = null,
        ?TagList $tags = null,
    ): self {
        $this->assertAuthoredBy($editorId);

        $retitled = $title !== null && $slug !== null;

        return new self(
            $this->id,
            $this->authorId,
            $retitled ? $slug : $this->slug,
            $retitled ? $title : $this->title,
            $description ?? $this->description,
            $body ?? $this->body,
            $tags ?? $this->tags,
        );
    }
}
