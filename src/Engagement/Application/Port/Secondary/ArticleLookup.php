<?php

namespace Conduit\Engagement\Application\Port\Secondary;

interface ArticleLookup
{
    public function idOfSlug(string $slug): ?int;
}
