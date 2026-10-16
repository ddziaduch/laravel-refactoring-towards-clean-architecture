<?php

namespace Tests\Unit\Engagement;

use Conduit\Engagement\Application\Exception\ArticleNotFound;
use Conduit\Engagement\Application\Exception\CommentNotFound;
use Conduit\Engagement\Application\Port\Secondary\ArticleLookup;
use Conduit\Engagement\Application\Port\Secondary\CommentRepository;
use Conduit\Engagement\Application\UseCase\AddCommentUseCase;
use Conduit\Engagement\Application\UseCase\DeleteCommentUseCase;
use Conduit\Engagement\Domain\Comment;
use Conduit\Engagement\Domain\Exception\CommentBodyCannotBeEmpty;
use Conduit\Engagement\Domain\Exception\NotCommentAuthor;
use PHPUnit\Framework\TestCase;

/**
 * The Engagement module is tested without the Article module: articles are
 * looked up through an in-memory implementation of Engagement's own port.
 */
class CommentUseCasesTest extends TestCase
{
    private InMemoryArticleLookup $articles;

    private InMemoryCommentRepository $comments;

    protected function setUp(): void
    {
        $this->articles = new InMemoryArticleLookup(['an-article' => 7, 'other-article' => 9]);
        $this->comments = new InMemoryCommentRepository;
    }

    public function test_it_adds_a_comment_to_an_existing_article(): void
    {
        $commentId = $this->addComment('an-article', 'A useful comment', 3);

        $this->assertEquals(new Comment($commentId, 7, 3, 'A useful comment'), $this->comments->find($commentId));
    }

    public function test_it_rejects_a_missing_article(): void
    {
        $this->expectException(ArticleNotFound::class);

        $this->addComment('missing-article', 'A useful comment', 3);
    }

    public function test_it_rejects_an_empty_body(): void
    {
        $this->expectException(CommentBodyCannotBeEmpty::class);

        $this->addComment('an-article', '   ', 3);
    }

    public function test_only_the_author_can_delete_a_comment(): void
    {
        $commentId = $this->addComment('an-article', 'A useful comment', 3);

        try {
            $this->deleteComment()('an-article', $commentId, 4);
            $this->fail('Expected the deletion to be forbidden.');
        } catch (NotCommentAuthor) {
            $this->assertNotNull($this->comments->find($commentId));
        }

        $this->deleteComment()('an-article', $commentId, 3);

        $this->assertNull($this->comments->find($commentId));
    }

    public function test_deletion_is_scoped_to_the_article(): void
    {
        $commentId = $this->addComment('an-article', 'A useful comment', 3);

        $this->expectException(CommentNotFound::class);

        $this->deleteComment()('other-article', $commentId, 3);
    }

    private function addComment(string $slug, string $body, int $authorId): int
    {
        return (new AddCommentUseCase($this->articles, $this->comments))($slug, $body, $authorId);
    }

    private function deleteComment(): DeleteCommentUseCase
    {
        return new DeleteCommentUseCase($this->articles, $this->comments);
    }
}

final readonly class InMemoryArticleLookup implements ArticleLookup
{
    /**
     * @param  array<string, int>  $idsBySlug
     */
    public function __construct(private array $idsBySlug) {}

    public function idOfSlug(string $slug): ?int
    {
        return $this->idsBySlug[$slug] ?? null;
    }
}

final class InMemoryCommentRepository implements CommentRepository
{
    /**
     * @var array<int, Comment>
     */
    private array $comments = [];

    private int $lastId = 0;

    public function add(Comment $comment): int
    {
        $id = ++$this->lastId;
        $this->comments[$id] = new Comment($id, $comment->articleId, $comment->authorId, $comment->body);

        return $id;
    }

    public function find(int $id): ?Comment
    {
        return $this->comments[$id] ?? null;
    }

    public function remove(Comment $comment): void
    {
        unset($this->comments[$comment->id]);
    }
}
