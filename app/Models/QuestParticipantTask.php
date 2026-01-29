<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    QuestParticipant,
    QuestTask,
    CompletionVerification
};

class QuestParticipantTask extends Model
{
    protected $fillable = [
        'quest_participant_id',
        'quest_task_id',
        'completion_status',
        'creator_approval',
        'completed_at',
        'approved_at'
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

    public function quest() {
        return $this->belongsTo(Quest::class);
    }

    public function completionVerification() {
        return $this->hasMany(CompletionVerification::class, 'quest_participant_task_id');
    }
}
