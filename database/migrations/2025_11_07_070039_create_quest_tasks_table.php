<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Quest;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quest_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Quest::class)->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('description');
            $table->integer('reward_exp');
            $table->integer('reward_points');
            $table->unsignedSmallInteger('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_tasks');
    }
};
