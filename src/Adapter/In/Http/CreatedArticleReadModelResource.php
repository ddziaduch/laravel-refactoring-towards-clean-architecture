<?php

namespace Clean\Adapter\In\Http;

use Clean\Application\ReadModel\ArticleReadModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ArticleReadModel $resource
 */
final class CreatedArticleReadModelResource extends JsonResource
{
    public static $wrap = 'article';

    public function toArray($request): array
    {
        return json_decode(json_encode($this->resource), associative: true);
    }
}
