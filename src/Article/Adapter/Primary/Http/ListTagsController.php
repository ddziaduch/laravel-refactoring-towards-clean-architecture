<?php

namespace Conduit\Article\Adapter\Primary\Http;

use Conduit\Article\Application\Port\Primary\ListTags;
use Illuminate\Http\JsonResponse;

final readonly class ListTagsController
{
    public function __construct(private ListTags $listTags) {}

    public function __invoke(): JsonResponse
    {
        return response()->json(['tags' => ($this->listTags)()]);
    }
}
