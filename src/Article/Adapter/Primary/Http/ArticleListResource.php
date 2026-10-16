<?php

namespace Conduit\Article\Adapter\Primary\Http;

use Conduit\Article\Application\ReadModel\ArticleList;
use Conduit\Article\Application\ReadModel\ArticleReadModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ArticleList $resource
 */
final class ArticleListResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'articles' => array_map(
                function (ArticleReadModel $article) use ($request): array {
                    $item = (new ArticleResource($article))->toArray($request);
                    unset($item['body']);

                    return $item;
                },
                $this->resource->articles,
            ),
            'articlesCount' => $this->resource->count,
        ];
    }
}
