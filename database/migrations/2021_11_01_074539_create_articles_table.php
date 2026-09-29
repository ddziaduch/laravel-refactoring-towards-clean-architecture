<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->string('description');
            $table->text('body');
            $table->timestamps(6);

            $table->index(['slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
