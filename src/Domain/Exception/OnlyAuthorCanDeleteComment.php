<?php

namespace Clean\Domain\Exception;

use DomainException;

final class OnlyAuthorCanDeleteComment extends DomainException
{
    public function __construct()
    {
        parent::__construct('Only the author can delete the comment.');
    }
}
