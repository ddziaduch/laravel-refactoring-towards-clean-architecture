<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

interface Slugger
{
    public function slugify(string $string): string;
}
