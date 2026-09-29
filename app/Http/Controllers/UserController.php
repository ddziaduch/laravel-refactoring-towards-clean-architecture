<?php

namespace App\Http\Controllers;

use App\Exceptions\ConduitException;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function show(): array
    {
        return $this->userResponse(auth()->getToken()->get());
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $user = $this->user->create($request->validated()['user']);

        auth()->login($user);

        return response()->json($this->userResponse(auth()->refresh()), Response::HTTP_CREATED);
    }

    public function update(UpdateRequest $request): array
    {
        auth()->user()->update($request->validated()['user']);

        return $this->userResponse(auth()->getToken()->get());
    }

    public function login(LoginRequest $request): array
    {
        if ($token = auth()->attempt($request->validated()['user'])) {
            return $this->userResponse($token);
        }

        throw new ConduitException(['credentials' => ['invalid']], Response::HTTP_UNAUTHORIZED);
    }

    protected function userResponse(string $jwtToken): array
    {
        $user = auth()->user();

        return ['user' => [
            'email' => $user->email,
            'token' => $jwtToken,
            'username' => $user->username,
            'bio' => $user->bio,
            'image' => $user->image,
        ]];
    }
}
