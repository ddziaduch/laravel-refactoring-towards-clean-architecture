<?php

namespace Tests\Unit;

use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Port\Secondary\ArticleRepository;
use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Application\UseCase\CreateCommentUseCase;
use Clean\Domain\Entity\Comment;
use Clean\Domain\Exception\CommentBodyCannotBeEmpty;
use PHPUnit\Framework\TestCase;

class CreateCommentUseCaseTest extends TestCase
{
    private InMemoryCommentRepository $comments;

    private CreateCommentUseCase $createComment;

    protected function setUp(): void
    {
        $this->comments = new InMemoryCommentRepository;
        $this->createComment = new CreateCommentUseCase(
            new InMemoryArticleRepository(['an-article' => 7]),
            $this->comments,
        );
    }

    public function test_it_creates_a_comment_for_an_existing_article(): void
    {
        $commentId = ($this->createComment)('an-article', 'A useful comment', 3);

        $this->assertSame(1, $commentId);
        $this->assertEquals([1 => new Comment(7, 3, 'A useful comment')], $this->comments->saved);
    }

    public function test_it_rejects_a_missing_article(): void
    {
        $this->expectException(ArticleNotFound::class);

        try {
            ($this->createComment)('missing-article', 'A useful comment', 3);
        } finally {
            $this->assertSame([], $this->comments->saved);
        }
    }

    public function test_it_rejects_an_empty_body(): void
    {
        $this->expectException(CommentBodyCannotBeEmpty::class);

        ($this->createComment)('an-article', '   ', 3);
    }
}

final readonly class InMemoryArticleRepository implements ArticleRepository
{
    /**
     * @param  array<string, int>  $idsBySlug
     */
    public function __construct(private array $idsBySlug) {}

    public function findIdBySlug(string $slug): ?int
    {
        return $this->idsBySlug[$slug] ?? null;
    }
}

final class InMemoryCommentRepository implements CommentRepository
{
    /**
     * @var array<int, Comment>
     */
    public array $saved = [];

    public function save(Comment $comment): int
    {
        $id = count($this->saved) + 1;
        $this->saved[$id] = $comment;

        return $id;
    }
}
