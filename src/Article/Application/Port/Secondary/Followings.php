<?php

namespace Conduit\Article\Application\Port\Secondary;

interface Followings
{
    /**
     * @return list<int> ids of the authors the reader follows
     */
    public function followedBy(int $readerId): array;
}
