<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createUser(string $username): User
    {
        return User::factory()->create([
            'username' => $username,
            'email' => $username.'@example.com',
            'password' => 'secret',
        ]);
    }

    protected function conduitHeaders(User $user): array
    {
        return ['Authorization' => 'Token '.app('tymon.jwt')->fromUser($user)];
    }

    public function getJson($uri, array $headers = [], $options = 0)
    {
        $this->resetAuthForNextRequest();

        return parent::getJson($uri, $headers, $options);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $this->resetAuthForNextRequest();

        return parent::postJson($uri, $data, $headers, $options);
    }

    public function putJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $this->resetAuthForNextRequest();

        return parent::putJson($uri, $data, $headers, $options);
    }

    public function deleteJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $this->resetAuthForNextRequest();

        return parent::deleteJson($uri, $data, $headers, $options);
    }

    private function resetAuthForNextRequest(): void
    {
        $this->app['auth']->forgetGuards();
        $this->app['tymon.jwt']->unsetToken();
    }
}
