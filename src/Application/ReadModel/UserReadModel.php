<?php

declare(strict_types=1);

namespace Clean\Application\ReadModel;

final class UserReadModel
{
    public string $username;
    public string $bio;
    public string $image;
    public array $followerIds;

    public function __construct(
        string $username,
        string $bio,
        string $image,
        array $followerIds
    ) {
        $this->followerIds = $followerIds;
        $this->image = $image;
        $this->bio = $bio;
        $this->username = $username;
    }
}
