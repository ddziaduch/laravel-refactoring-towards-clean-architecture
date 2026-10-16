<?php

namespace Conduit\Article\Adapter\Secondary\Laravel;

use Conduit\Article\Application\Port\Secondary\Slugger;
use Illuminate\Support\Str;

final class StrSlugger implements Slugger
{
    public function slugify(string $text): string
    {
        return Str::slug($text);
    }
}
