<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Media extends Model
{
    protected $fillable = [
        'user_id',
        'filepath',
        'social_activity_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function socialActivity() {
        return $this->belongsTo(SocialActivity::class, 'social_activity_id');
    }
}
