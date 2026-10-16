<?php

namespace Conduit\Identity\Application\Port\Secondary;

interface TokenIssuer
{
    public function issueFor(int $userId): string;
}
