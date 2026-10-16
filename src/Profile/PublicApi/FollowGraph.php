<?php

namespace Conduit\Profile\PublicApi;

interface FollowGraph
{
    /**
     * @return list<int> ids of the users followed by the given user
     */
    public function followedBy(int $userId): array;
}
