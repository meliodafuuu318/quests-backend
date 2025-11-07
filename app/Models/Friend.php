<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Friend extends Model
{
    protected $fillable = [
        'user_id',
        'friend_id',
        'status'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend() {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
