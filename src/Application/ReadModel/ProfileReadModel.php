<?php

declare(strict_types=1);

namespace Clean\Application\ReadModel;

final class ProfileReadModel
{
    public int $id;
    public string $username;
    public ?string $bio;
    public ?string $image;
    public bool $following;

    public function __construct(
        int $id,
        string $username,
        ?string $bio,
        ?string $image,
        bool $following
    ) {
        $this->image = $image;
        $this->bio = $bio;
        $this->username = $username;
        $this->id = $id;
        $this->following = $following;
    }
}
