<?php

namespace Clean\Adapter\Primary\Cli;

use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Port\Primary\CreateComment;
use Clean\Domain\Exception\CommentBodyCannotBeEmpty;
use Illuminate\Console\Command;

final class CreateCommentCommand extends Command
{
    protected $signature = 'comment:create
        {article : The slug of the commented article}
        {author : The ID of the comment author}
        {body : The comment body}';

    protected $description = 'Comment an article';

    public function handle(CreateComment $createComment): int
    {
        try {
            $commentId = $createComment(
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
