<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopPositionByCategory extends Model
{
    protected $fillable = [
        'date',
        'category_id',
        'position',
    ];
}
