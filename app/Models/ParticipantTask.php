<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    User,
    Quest,
    QuestTask
};

class ParticipantTask extends Model
{
    protected $fillable = [
        'user_id',
        'quest_id',
        'quest_task_id',
        'status',
        'completed_at'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quest() {
        return $this->belongsTo(Quest::class, 'quest_id');
    }
    
    public function questTask() {
        return $this->belongsTo(QuestTask::class, 'quest_task_id');
    }
}
