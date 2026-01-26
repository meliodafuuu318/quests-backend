<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'type',
        'filepath'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
