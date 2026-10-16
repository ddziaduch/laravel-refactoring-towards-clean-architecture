<?php

namespace Conduit\Identity\Adapter\Secondary\Eloquent;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Persistence model of the users table. It is also the authenticatable model of
 * the JWT guard, which is why it lives in the module that owns the table.
 */
final class UserModel extends Authenticatable implements JWTSubject
{
    protected $table = 'users';

    protected $fillable = ['username', 'email', 'password', 'bio', 'image'];

    protected $hidden = ['password'];

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
