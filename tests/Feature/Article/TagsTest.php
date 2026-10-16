<?php

namespace Tests\Feature\Article;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tags_are_listed_once(): void
    {
        $this->getJson('/api/tags')->assertOk()->assertExactJson(['tags' => []]);

        $author = $this->registerUser('author');
        $this->publishArticle($author, 'First', ['tagList' => ['php', 'laravel']]);
        $this->publishArticle($author, 'Second', ['tagList' => ['laravel', 'architecture']]);

        $this->getJson('/api/tags')
            ->assertOk()
            ->assertExactJson(['tags' => ['php', 'laravel', 'architecture']]);
    }
}
