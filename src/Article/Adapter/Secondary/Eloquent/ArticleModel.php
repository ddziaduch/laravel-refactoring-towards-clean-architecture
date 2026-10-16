<?php

namespace Conduit\Article\Adapter\Secondary\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Relations stay inside the module: tags yes, users or comments never.
 */
final class ArticleModel extends Model
{
    protected $table = 'articles';

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = ['user_id', 'slug', 'title', 'description', 'body'];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(TagModel::class, 'article_tag', 'article_id', 'tag_id')->orderBy('tags.id');
    }
}
