<?php

namespace Clean\Application\Exception;

use RuntimeException;

final class CommentNotFound extends RuntimeException
{
    public static function withId(int $id): self
    {
        return new self(sprintf('Comment #%d not found.', $id));
    }
}
