<?php

namespace Conduit\Engagement\Domain\Exception;

use DomainException;

final class NotCommentAuthor extends DomainException
{
    public static function of(int $commentId, int $userId): self
    {
        return new self(sprintf('User #%d is not the author of comment #%d.', $userId, $commentId));
    }
}
