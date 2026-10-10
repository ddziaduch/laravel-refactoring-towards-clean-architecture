<?php

namespace Clean\Adapter\Secondary\Clock;

use Clean\Application\Port\Secondary\Clock;
use DateTimeImmutable;

final class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable;
    }
}
