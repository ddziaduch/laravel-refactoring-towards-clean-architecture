<?php

namespace Conduit\Article\Application\Port\Secondary;

interface Slugger
{
    public function slugify(string $text): string;
}
