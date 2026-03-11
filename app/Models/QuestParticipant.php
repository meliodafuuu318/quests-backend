<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    Quest,
    User,
    QuestParticipantTask
};

class QuestParticipant extends Model
{
    protected $fillable = [
        'user_id',
        'quest_id',
        'joined_at',
        'completed_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function quest() {
        return $this->belongsTo(Quest::class, 'quest_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function questParticipantTask() {
        return $this->hasMany(QuestParticipantTask::class, 'quest_participant_id');
    }
}
