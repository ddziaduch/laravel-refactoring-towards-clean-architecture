<?php

namespace Conduit\Profile\Adapter\Primary\Http;

use App\Exceptions\ConduitException;
use Closure;
use Conduit\Profile\Application\Exception\ProfileNotFound;
use Conduit\Profile\Application\Port\Primary\FollowUser;
use Conduit\Profile\Application\Port\Primary\GetProfile;
use Conduit\Profile\Application\Port\Primary\UnfollowUser;
use Conduit\Profile\Application\ReadModel\ProfileReadModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ProfileController
{
    public function show(string $username, GetProfile $getProfile, Request $request): ProfileResource
    {
        return $this->respond(fn (): ProfileReadModel => $getProfile($username, $this->viewerId($request)));
    }

    public function follow(string $username, FollowUser $followUser, Request $request): ProfileResource
    {
        return $this->respond(fn (): ProfileReadModel => $followUser($this->viewerId($request), $username));
    }

    public function unfollow(string $username, UnfollowUser $unfollowUser, Request $request): ProfileResource
    {
        return $this->respond(fn (): ProfileReadModel => $unfollowUser($this->viewerId($request), $username));
    }

    private function viewerId(Request $request): ?int
    {
        return $request->user()?->getAuthIdentifier();
    }

    /**
     * @param  Closure(): ProfileReadModel  $useCase
     */
    private function respond(Closure $useCase): ProfileResource
    {
        try {
            return new ProfileResource($useCase());
        } catch (ProfileNotFound) {
            throw new ConduitException(['profile' => ['not found']], Response::HTTP_NOT_FOUND);
        }
    }
}
