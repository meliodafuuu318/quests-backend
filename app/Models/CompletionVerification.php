<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    User,
    QuestParticipantTask
};

class CompletionVerification extends Model
{
    protected $fillable = [
        'quest_participant_task_id',
        'type',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function questParticipantTask() {
        return $this->belongsTo(QuestParticipantTask::class, 'quest_participant_task_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
