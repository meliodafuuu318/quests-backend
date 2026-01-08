<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    User,
    Quest
};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialActivity extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'id',
        'user_id',
        'type',
        'visibility',
        'title',
        'content',
        'comment_target',
        'like_target',
        'created_at',
        'updated_at',
        // 'asset_id'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
