<?php

namespace Conduit\Profile\PublicApi;

interface Profiles
{
    /**
     * Profiles as seen by the viewer, in one call for the whole page.
     *
     * @param  list<int>  $userIds
     * @return array<int, ProfileView> keyed by user id, unknown ids are skipped
     */
    public function profilesOf(array $userIds, ?int $viewerId): array;

    public function userIdOf(string $username): ?int;
}
