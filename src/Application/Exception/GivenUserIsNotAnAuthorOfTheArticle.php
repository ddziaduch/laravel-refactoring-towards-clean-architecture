<?php

declare(strict_types=1);

namespace Clean\Application\Exception;

use RuntimeException;

final class GivenUserIsNotAnAuthorOfTheArticle extends RuntimeException
{
    public function __construct(int $userId, string $articleSlug)
    {
        parent::__construct(sprintf('User ID %s is not an author of article with the slug %s.', $userId, $articleSlug));
    }
}
