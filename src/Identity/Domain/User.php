<?php

namespace Conduit\Identity\Domain;

/**
 * A user account: who you are and how you prove it.
 */
final readonly class User
{
    public function __construct(
        public ?int $id,
        public Username $username,
        public Email $email,
        public HashedPassword $password,
        public ?string $bio = null,
        public ?string $image = null,
    ) {}

    public static function register(Username $username, Email $email, HashedPassword $password): self
    {
        return new self(null, $username, $email, $password);
    }

    public function rename(Username $username): self
    {
        return new self($this->id, $username, $this->email, $this->password, $this->bio, $this->image);
    }

    public function changeEmail(Email $email): self
    {
        return new self($this->id, $this->username, $email, $this->password, $this->bio, $this->image);
    }

    public function changePassword(HashedPassword $password): self
    {
        return new self($this->id, $this->username, $this->email, $password, $this->bio, $this->image);
    }

    public function changeBio(?string $bio): self
    {
        return new self($this->id, $this->username, $this->email, $this->password, $bio, $this->image);
    }

    public function changeImage(?string $image): self
    {
        return new self($this->id, $this->username, $this->email, $this->password, $this->bio, $image);
    }
}
