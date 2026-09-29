<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Deterministic demo data so every workshop participant sees the same API.
     *
     * Every seeded user has the password "password".
     */
    private const PASSWORD = 'password';

    public function run(): void
    {
        $users = $this->seedUsers();
        $articles = $this->seedArticles($users);

        $this->seedComments($users, $articles);
        $this->seedFollows($users);
        $this->seedFavorites($users, $articles);
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        $users = [];

        foreach ([
            ['username' => 'john', 'email' => 'john@example.com', 'bio' => 'Backend developer, fan of boring technology.'],
            ['username' => 'jane', 'email' => 'jane@example.com', 'bio' => 'Architect. Writes about layered designs.'],
            ['username' => 'alice', 'email' => 'alice@example.com', 'bio' => null],
        ] as $attributes) {
            $users[$attributes['username']] = User::query()->create($attributes + [
                'password' => self::PASSWORD,
                'image' => null,
            ]);
        }

        return $users;
    }

    /**
     * @param  array<string, User>  $users
     * @return array<string, Article>
     */
    private function seedArticles(array $users): array
    {
        $definitions = [
            ['author' => 'john', 'title' => 'How to build a Conduit backend', 'tags' => ['laravel', 'conduit']],
            ['author' => 'john', 'title' => 'Eloquent is not your domain model', 'tags' => ['laravel', 'architecture']],
            ['author' => 'jane', 'title' => 'Ports and adapters in practice', 'tags' => ['architecture', 'testing']],
            ['author' => 'jane', 'title' => 'Why we stopped writing service classes', 'tags' => ['architecture']],
            ['author' => 'alice', 'title' => 'Testing an API against a real database', 'tags' => ['testing', 'postgres']],
            ['author' => 'alice', 'title' => 'Slugs, uniqueness and other small lies', 'tags' => ['conduit']],
        ];

        $articles = [];

        foreach ($definitions as $definition) {
            $article = $users[$definition['author']]->articles()->create([
                'title' => $definition['title'],
                'description' => 'A short summary of "'.$definition['title'].'".',
                'body' => "## {$definition['title']}\n\nSeeded article body used by the workshop fixtures.",
            ]);

            $article->tags()->sync(
                collect($definition['tags'])
                    ->map(fn (string $name): int => Tag::query()->firstOrCreate(['name' => $name])->id)
                    ->all(),
            );

            $articles[$article->slug] = $article;
        }

        return $articles;
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Article>  $articles
     */
    private function seedComments(array $users, array $articles): void
    {
        $articles = array_values($articles);

        $comments = [
            ['article' => 0, 'author' => 'jane', 'body' => 'Great write-up, the slug handling is the tricky part.'],
            ['article' => 0, 'author' => 'alice', 'body' => 'Do you keep the tag list in a pivot table?'],
            ['article' => 2, 'author' => 'john', 'body' => 'This finally made hexagonal architecture click for me.'],
            ['article' => 4, 'author' => 'jane', 'body' => 'Running the suite against Postgres caught a real bug for us.'],
        ];

        foreach ($comments as $comment) {
            Comment::query()->create([
                'article_id' => $articles[$comment['article']]->id,
                'user_id' => $users[$comment['author']]->id,
                'body' => $comment['body'],
            ]);
        }
    }

    /**
     * @param  array<string, User>  $users
     */
    private function seedFollows(array $users): void
    {
        $users['john']->following()->sync([$users['jane']->id]);
        $users['alice']->following()->sync([$users['john']->id, $users['jane']->id]);
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Article>  $articles
     */
    private function seedFavorites(array $users, array $articles): void
    {
        $articles = array_values($articles);

        $users['john']->favoritedArticles()->sync([$articles[2]->id, $articles[4]->id]);
        $users['jane']->favoritedArticles()->sync([$articles[0]->id]);
        $users['alice']->favoritedArticles()->sync([$articles[0]->id, $articles[2]->id]);
    }
}
