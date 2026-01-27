<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    UserAchievement,
    Asset
};

class Achievement extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'exp_reward',
        'asset_id'
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
