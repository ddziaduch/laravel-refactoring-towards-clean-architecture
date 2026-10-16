<?php

namespace Conduit\Engagement\Domain\Exception;

use DomainException;

final class CommentBodyCannotBeEmpty extends DomainException
{
    public function __construct()
    {
        parent::__construct('Comment body cannot be empty.');
    }
}
