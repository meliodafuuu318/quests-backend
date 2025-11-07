<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    User,
    Achievement,
};

class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'achievement_id',
        'obtained_at'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function achievement() {
        return $this->belongsTo(Achievement::class, 'achievement_id');
    }
}
