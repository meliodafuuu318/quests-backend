<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    UserAchievement
};

class Achievement extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'exp_reward',
        'icon',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function userAchievement() {
        return $this->hasMany(UserAchievement::class);
    }
}
