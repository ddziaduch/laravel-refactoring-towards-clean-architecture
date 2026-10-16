<?php

namespace Conduit\Identity\Application\ReadModel;

use Conduit\Identity\Domain\User;

final readonly class UserReadModel
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email,
        public ?string $bio,
        public ?string $image,
    ) {}

    public static function fromUser(User $user): self
    {
        return new self(
            $user->id,
            $user->username->value,
            $user->email->value,
            $user->bio,
            $user->image,
        );
    }
}
