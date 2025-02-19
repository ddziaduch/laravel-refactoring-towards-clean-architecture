<?php

namespace Clean\Application\ReadModel;

class ProfileReadModel
{
    public string $username;
    public ?string $bio;
    public ?string $image;
    public bool $following;

    public function __construct(
        string $username,
        ?string $bio,
        ?string $image,
        bool $following
    ) {
        $this->following = $following;
        $this->image = $image;
        $this->bio = $bio;
        $this->username = $username;
    }
}
