<?php

namespace Database\Seeders;

use Conduit\Article\Application\Port\Primary\FavoriteArticle;
use Conduit\Article\Application\Port\Primary\PublishArticle;
use Conduit\Engagement\Application\Port\Primary\AddComment;
use Conduit\Identity\Application\Port\Primary\RegisterUser;
use Conduit\Identity\Application\Port\Primary\UpdateUser;
use Conduit\Identity\Application\Port\Primary\UserChanges;
use Conduit\Profile\Application\Port\Primary\FollowUser;
use Illuminate\Database\Seeder;

/**
 * Seeds through the modules' primary ports, exactly like any other client would.
 */
class DatabaseSeeder extends Seeder
{
    private const PASSWORD = 'password';

    public function run(
        RegisterUser $registerUser,
        UpdateUser $updateUser,
        PublishArticle $publishArticle,
        AddComment $addComment,
        FollowUser $followUser,
        FavoriteArticle $favoriteArticle,
    ): void {
        $users = [];

        foreach ([
            'john' => 'Backend developer, fan of boring technology.',
            'jane' => 'Architect. Writes about layered designs.',
            'alice' => null,
        ] as $username => $bio) {
            $users[$username] = $registerUser($username, $username.'@example.com', self::PASSWORD)->user->id;
            $updateUser($users[$username], new UserChanges(['bio' => $bio]));
        }

        $slugs = [];

        foreach ([
            ['author' => 'john', 'title' => 'How to build a Conduit backend', 'tags' => ['laravel', 'conduit']],
            ['author' => 'john', 'title' => 'Eloquent is not your domain model', 'tags' => ['laravel', 'architecture']],
            ['author' => 'jane', 'title' => 'Ports and adapters in practice', 'tags' => ['architecture', 'testing']],
            ['author' => 'jane', 'title' => 'Why we stopped writing service classes', 'tags' => ['architecture']],
            ['author' => 'alice', 'title' => 'Testing an API against a real database', 'tags' => ['testing', 'postgres']],
            ['author' => 'alice', 'title' => 'Slugs, uniqueness and other small lies', 'tags' => ['conduit']],
        ] as $article) {
            $slugs[] = $publishArticle(
                $users[$article['author']],
                $article['title'],
                'A short summary of "'.$article['title'].'".',
                "## {$article['title']}\n\nSeeded article body used by the workshop fixtures.",
                $article['tags'],
            );
        }

        foreach ([
            [0, 'jane', 'Great write-up, the slug handling is the tricky part.'],
            [0, 'alice', 'Do you keep the tag list in a pivot table?'],
            [2, 'john', 'This finally made hexagonal architecture click for me.'],
            [4, 'jane', 'Running the suite against Postgres caught a real bug for us.'],
        ] as [$article, $author, $body]) {
            $addComment($slugs[$article], $body, $users[$author]);
        }

        foreach ([['john', 'jane'], ['alice', 'john'], ['alice', 'jane']] as [$follower, $followed]) {
            $followUser($users[$follower], $followed);
        }

        foreach ([['john', 2], ['john', 4], ['jane', 0], ['alice', 0], ['alice', 2]] as [$reader, $article]) {
            $favoriteArticle($slugs[$article], $users[$reader]);
        }
    }
}
