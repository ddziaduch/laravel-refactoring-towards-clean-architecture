<?php

namespace Tests;

final readonly class TestUser
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email,
        public string $token,
    ) {}

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return ['Authorization' => 'Token '.$this->token];
    }
}
