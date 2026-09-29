<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    public static $wrap = '';

    public $collects = ArticleListResource::class;

    public function __construct($resource, protected int $articlesCount)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'articles' => $this->collection,
            'articlesCount' => $this->articlesCount,
        ];
    }
}
