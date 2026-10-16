<?php

namespace Conduit\Article\Adapter\Primary\Http;

use App\Exceptions\ConduitException;
use Closure;
use Conduit\Article\Application\Exception\ArticleNotFound;
use Conduit\Article\Domain\Exception\NotArticleAuthor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Maps the module's exceptions to the Conduit error format, and reads the
 * viewer from the request (controllers are reused, so nothing is cached).
 */
trait TranslatesArticleErrors
{
    /**
     * @template T
     *
     * @param  Closure(): T  $action
     * @return T
     */
    private function translateErrors(Closure $action): mixed
    {
        try {
            return $action();
        } catch (ArticleNotFound) {
            throw new ConduitException(['article' => ['not found']], Response::HTTP_NOT_FOUND);
        } catch (NotArticleAuthor) {
            throw new ConduitException(['article' => ['forbidden']], Response::HTTP_FORBIDDEN);
        }
    }

    private function viewerId(Request $request): ?int
    {
        return $request->user()?->getAuthIdentifier();
    }
}
