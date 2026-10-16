<?php

namespace Tests\Unit\Article;

use Conduit\Article\Domain\Article;
use Conduit\Article\Domain\Exception\NotArticleAuthor;
use Conduit\Article\Domain\TagList;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function test_only_the_author_can_edit_an_article(): void
    {
        $article = Article::publish(7, 'a-title', 'A title', 'Description', 'Body', new TagList([]));

        $this->expectException(NotArticleAuthor::class);

        $article->edit(8, body: 'Changed');
    }

    public function test_title_and_slug_change_together(): void
    {
        $article = Article::publish(7, 'a-title', 'A title', 'Description', 'Body', new TagList(['php']));

        $edited = $article->edit(7, title: 'New title', slug: 'new-title', body: 'Changed');

        $this->assertSame(['New title', 'new-title', 'Changed'], [$edited->title, $edited->slug, $edited->body]);
        $this->assertSame(['php'], $edited->tags->names);
        $this->assertSame('a-title', $article->slug);
    }

    public function test_tags_are_trimmed_unique_and_non_empty(): void
    {
        $this->assertSame(['php', 'laravel'], (new TagList([' php', 'laravel', 'php ', '']))->names);
    }
}
