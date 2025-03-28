<?php
declare(strict_types=1);

namespace Clean\Application\Port\In;

use Clean\Domain\Exception\ArticleNotFound;

interface CreateCommentUserCaseInterface
{
    /**
     * @throws ArticleNotFound
     */
    public function __invoke(
        string $articleSlug,
        int $authorId,
        string $commentBody
    ): int;
}
