<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'filepath'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
