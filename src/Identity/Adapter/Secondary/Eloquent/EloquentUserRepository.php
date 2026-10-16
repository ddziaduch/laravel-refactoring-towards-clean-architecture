<?php

namespace Conduit\Identity\Adapter\Secondary\Eloquent;

use Conduit\Identity\Application\Port\Secondary\UserRepository;
use Conduit\Identity\Domain\Email;
use Conduit\Identity\Domain\HashedPassword;
use Conduit\Identity\Domain\User;
use Conduit\Identity\Domain\Username;

final class EloquentUserRepository implements UserRepository
{
    public function add(User $user): int
    {
        return UserModel::query()->create($this->attributes($user))->getKey();
    }

    public function save(User $user): void
    {
        UserModel::query()->findOrFail($user->id)->update($this->attributes($user));
    }

    public function find(int $id): ?User
    {
        return $this->toDomain(UserModel::query()->find($id));
    }

    public function findByEmail(string $email): ?User
    {
        return $this->toDomain(UserModel::query()->where('email', $email)->first());
    }

    public function findByUsername(string $username): ?User
    {
        return $this->toDomain(UserModel::query()->where('username', $username)->first());
    }

    public function findMany(array $ids): array
    {
        return UserModel::query()
            ->whereKey($ids)
            ->get()
            ->map(fn (UserModel $model): User => $this->toDomain($model))
            ->all();
    }

    private function attributes(User $user): array
    {
        return [
            'username' => $user->username->value,
            'email' => $user->email->value,
            'password' => $user->password->value,
            'bio' => $user->bio,
            'image' => $user->image,
        ];
    }

    private function toDomain(?UserModel $model): ?User
    {
        if ($model === null) {
            return null;
        }

        return new User(
            $model->getKey(),
            new Username($model->username),
            new Email($model->email),
            new HashedPassword($model->password),
            $model->bio,
            $model->image,
        );
    }
}
