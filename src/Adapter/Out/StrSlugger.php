<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use Clean\Application\Port\Out\Slugger;
use Illuminate\Support\Str;

final class StrSlugger implements Slugger
{
    public function slugify(string $string): string
    {
        return Str::slug($string);
    }
}
