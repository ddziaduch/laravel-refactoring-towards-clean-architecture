<?php

namespace Clean\Application\Port\Secondary;

use DateTimeImmutable;

interface Clock
{
    public function now(): DateTimeImmutable;
}
