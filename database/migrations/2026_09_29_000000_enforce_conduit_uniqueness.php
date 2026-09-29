<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->unique('slug');
        });

        Schema::table('article_tag', function (Blueprint $table): void {
            $table->unique(['article_id', 'tag_id']);
        });

        Schema::table('article_user', function (Blueprint $table): void {
            $table->unique(['article_id', 'user_id']);
        });

        Schema::table('followers', function (Blueprint $table): void {
            $table->unique(['follower_id', 'following_id']);
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
        });

        Schema::table('article_tag', function (Blueprint $table): void {
            $table->dropUnique(['article_id', 'tag_id']);
        });

        Schema::table('article_user', function (Blueprint $table): void {
            $table->dropUnique(['article_id', 'user_id']);
        });

        Schema::table('followers', function (Blueprint $table): void {
            $table->dropUnique(['follower_id', 'following_id']);
        });
    }
};
