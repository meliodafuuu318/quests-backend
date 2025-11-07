<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{
    User,
    ShopItem
};

class UserItem extends Model
{
    protected $fillable = [
        'user_id',
        'shop_item_id',
        'equipped',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shopItem() {
        return $this->belongsTo(ShopItem::class, 'shop_item_id');
    }
}
