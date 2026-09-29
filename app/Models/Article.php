<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = ['title', 'description', 'body'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return array{articles: Collection, count: int}
     */
    public function getFiltered(array $filters, ?User $feedFor = null): array
    {
        $query = $this->filter($filters, 'tag', 'tags', 'name')
            ->filter($filters, 'author', 'user', 'username')
            ->filter($filters, 'favorited', 'users', 'username');

        if ($feedFor !== null) {
            $query->whereIn('user_id', $feedFor->following()->pluck('users.id'));
        }

        $count = (clone $query)->count();
        $articles = $query
            ->latest('created_at')
            ->latest('id')
            ->offset($filters['offset'] ?? 0)
            ->limit($filters['limit'] ?? 20)
            ->with('user', 'users', 'tags', 'user.followers')
            ->withCount('users')
            ->get();

        return ['articles' => $articles, 'count' => $count];
    }

    public function scopeFilter($query, array $filters, string $key, string $relation, string $column)
    {
        return $query->when(array_key_exists($key, $filters), function ($q) use ($filters, $relation, $column, $key) {
            $q->whereRelation($relation, $column, $filters[$key]);
        });
    }

    public function setTitleAttribute(string $title): void
    {
        $this->attributes['title'] = $title;

        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $suffix = 1;

        while ($this->newQuery()
            ->where('slug', $slug)
            ->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        $this->attributes['slug'] = $slug;
    }
}
