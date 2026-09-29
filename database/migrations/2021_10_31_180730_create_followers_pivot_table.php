<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table): void {
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');

            $table->index(['follower_id', 'following_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
