<?php

namespace Conduit\Article\PublicApi;

interface Articles
{
    /**
     * Other modules refer to articles by id; the slug is how users name them.
     */
    public function idOfSlug(string $slug): ?int;
}
