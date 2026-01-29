<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    Quest,
    QuestParticipantTask
};

class QuestTask extends Model
{
    protected $fillable = [
        'quest_id',
        'title',
        'description',
        'reward_exp',
        'reward_points',
        'order'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function quest() {
        return $this->belongsTo(Quest::class, 'quest_id');
    }

    public function questParticipantTask() {
        return $this->hasMany(QuestParticipantTask::class);
    }
}
