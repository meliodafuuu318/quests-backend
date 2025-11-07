<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    User,
    Quest
};

class SocialActivity extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'type',
        'comment_target',
        'like_target',
        'created_at',
        'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
