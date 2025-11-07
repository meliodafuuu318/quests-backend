<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    Quest,
    User
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
        'id',
        'created_at',
        'updated_at'
    ];

    public function quest() {
        return $this->belongsTo(Quest::class, 'quest_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
