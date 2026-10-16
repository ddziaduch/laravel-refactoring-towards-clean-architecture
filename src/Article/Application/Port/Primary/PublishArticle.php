<?php

namespace Conduit\Article\Application\Port\Primary;

interface PublishArticle
{
    /**
     * @param  list<string>  $tagList
     * @return string the slug of the published article
     */
    public function __invoke(int $authorId, string $title, string $description, string $body, array $tagList): string;
}
