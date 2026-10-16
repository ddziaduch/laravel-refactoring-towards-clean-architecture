<?php

namespace Conduit\Article\Adapter\Secondary\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class TagModel extends Model
{
    public $timestamps = false;

    protected $table = 'tags';

    protected $fillable = ['name'];
}
