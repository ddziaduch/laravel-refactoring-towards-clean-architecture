<?php

namespace Conduit\Article\Application\Port\Primary;

interface ListTags
{
    /**
     * @return list<string>
     */
    public function __invoke(): array;
}
