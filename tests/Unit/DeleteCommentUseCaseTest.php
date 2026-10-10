<?php

namespace Tests\Unit;

use Clean\Application\Exception\CommentNotFound;
use Clean\Application\UseCase\DeleteCommentUseCase;
use Clean\Domain\Entity\Comment;
use Clean\Domain\Exception\OnlyAuthorCanDeleteComment;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DeleteCommentUseCaseTest extends TestCase
{
    private InMemoryCommentRepository $comments;

    private DeleteCommentUseCase $deleteComment;

    private DateTimeImmutable $now;

    private int $commentId;

    protected function setUp(): void
    {
        $this->now = new DateTimeImmutable('2026-10-01 12:34:56');
        $this->comments = new InMemoryCommentRepository;
        $this->deleteComment = new DeleteCommentUseCase(
            new InMemoryArticleRepository(['an-article' => 7, 'other-article' => 8]),
            $this->comments,
            new FrozenClock($this->now),
        );
        $this->commentId = $this->comments->save(new Comment(7, 3, 'A useful comment'));
    }

    public function test_author_deletes_their_comment(): void
    {
        ($this->deleteComment)('an-article', $this->commentId, 3);

        $this->assertEquals($this->now, $this->comments->find($this->commentId)->deletedAt());
    }

    public function test_it_rejects_an_already_deleted_comment(): void
    {
        ($this->deleteComment)('an-article', $this->commentId, 3);

        $this->expectException(CommentNotFound::class);

        ($this->deleteComment)('an-article', $this->commentId, 3);
    }

    public function test_it_rejects_a_comment_of_another_article(): void
    {
        $this->expectException(CommentNotFound::class);

        ($this->deleteComment)('other-article', $this->commentId, 3);
    }

    public function test_it_rejects_a_user_who_is_not_the_author(): void
    {
        $this->expectException(OnlyAuthorCanDeleteComment::class);

        try {
            ($this->deleteComment)('an-article', $this->commentId, 4);
        } finally {
            $this->assertFalse($this->comments->find($this->commentId)->isDeleted());
        }
    }
}
