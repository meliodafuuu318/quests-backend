<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    User,
    QuestTask,
    QuestParticipant,
    ParticipantTask,
    SocialActivity
};

class Quest extends Model
{
    protected $fillable = [
        'code',
        'post_id',
        'creator_id',
        // 'title',
        // 'description',
        // 'visibility',
        'reward_exp',
        'reward_points',
        'participant_count',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function questTask() {
        return $this->hasMany(QuestTask::class);
    }

    public function questParticipant() {
        return $this->hasMany(QuestParticipant::class);
    }

    public function participantTask() {
        return $this->hasMany(ParticipantTask::class);
    }

    public function socialActivity() {
        return $this->belongsTo(SocialActivity::class, 'post_id');
    }
}
