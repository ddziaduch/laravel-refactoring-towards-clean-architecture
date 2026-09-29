<?php

namespace App\Exceptions;

use RuntimeException;

class ConduitException extends RuntimeException
{
    public function __construct(
        public readonly array $errors,
        public readonly int $status,
    ) {
        parent::__construct();
    }
}
