<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\{
    User,
    QuestParticipantTask
};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('completion_verifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['verification', 'flag'])->default('verification');
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(QuestParticipantTask::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completion_verifications');
    }
};
