<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    UserItem
};

class ShopItem extends Model
{
    protected $fillable = [
        'name',
        'type',
        'price',
        // 'asset_id'
    ];
    
    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function userItem() {
        return $this->hasMany(UserItem::class);
    }
}
