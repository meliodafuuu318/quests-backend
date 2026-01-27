<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\{
    User,
    Media
};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->enum('type', ['post', 'comment', 'like'])->default('post');
            $table->enum('visibility', ['public', 'friends', 'private'])->default('public');
            $table->string('title')->nullable();
            $table->string('content')->nullable();
            $table->foreignIdFor(Media::class)->constrained()->nullable();
            $table->integer('comment_target')->nullable();
            $table->integer('like_target')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_activities');
    }
};
