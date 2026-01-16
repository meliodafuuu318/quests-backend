<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\{
    QuestTask,
    QuestParticipant
};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quest_participant_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(QuestTask::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(QuestParticipant::class)->constrained()->cascadeOnDelete();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_participant_tasks');
    }
};
