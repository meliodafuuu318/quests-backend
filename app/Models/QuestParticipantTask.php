<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    QuestParticipant,
    QuestTask
};

class QuestParticipantTask extends Model
{
    protected $fillable = [
        'quest_participant_id',
        'quest_task_id',
        'completed_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function questParticipant() {
        return $this->belongsTo(QuestParticipant::class, 'quest_participant_id');
    }

    public function questTask() {
        return $this->belongsTo(QuestTask::class, 'quest_task_id');
    }
}
