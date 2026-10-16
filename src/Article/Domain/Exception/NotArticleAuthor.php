<?php

namespace Conduit\Article\Domain\Exception;

use DomainException;

final class NotArticleAuthor extends DomainException
{
    public static function of(string $slug, int $userId): self
    {
        return new self(sprintf('User #%d is not the author of article "%s".', $userId, $slug));
    }
}
