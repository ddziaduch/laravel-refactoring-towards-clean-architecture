<?php

namespace Conduit\Identity\Application\Port\Secondary;

use Conduit\Identity\Domain\User;

interface UserRepository
{
    public function add(User $user): int;

    public function save(User $user): void;

    public function find(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findByUsername(string $username): ?User;

    /**
     * @param  list<int>  $ids
     * @return list<User>
     */
    public function findMany(array $ids): array;
}
