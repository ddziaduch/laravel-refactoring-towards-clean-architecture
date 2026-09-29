<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Article::class)->constrained()->onDelete('cascade');
            $table->text('body');
            $table->timestamps(6);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
