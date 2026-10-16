<?php

namespace Conduit\Engagement\Adapter\Primary\Cli;

use Conduit\Engagement\Application\Exception\ArticleNotFound;
use Conduit\Engagement\Application\Port\Primary\AddComment;
use Conduit\Engagement\Domain\Exception\CommentBodyCannotBeEmpty;
use Illuminate\Console\Command;

/**
 * A second primary adapter driving the same use case as the HTTP controller.
 */
final class AddCommentCommand extends Command
{
    protected $signature = 'comment:create
        {article : The slug of the commented article}
        {author : The ID of the comment author}
        {body : The comment body}';

    protected $description = 'Comment an article';

    public function handle(AddComment $addComment): int
    {
        try {
            $commentId = $addComment(
                $this->argument('article'),
                $this->argument('body'),
                (int) $this->argument('author'),
            );
        } catch (ArticleNotFound|CommentBodyCannotBeEmpty $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf('Comment #%d created.', $commentId));

        return self::SUCCESS;
    }
}
