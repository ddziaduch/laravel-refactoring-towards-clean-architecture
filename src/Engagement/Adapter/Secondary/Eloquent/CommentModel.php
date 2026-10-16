<?php

namespace Conduit\Engagement\Adapter\Secondary\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * No relations: the article and the user belong to other modules.
 */
final class CommentModel extends Model
{
    protected $table = 'comments';

    protected $fillable = ['article_id', 'user_id', 'body'];
}
