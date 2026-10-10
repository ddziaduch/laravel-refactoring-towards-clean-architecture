<?php

namespace Tests\Unit;

use Clean\Application\Port\Secondary\Clock;
use DateTimeImmutable;

final readonly class FrozenClock implements Clock
{
    public function __construct(private DateTimeImmutable $now) {}

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }
}
