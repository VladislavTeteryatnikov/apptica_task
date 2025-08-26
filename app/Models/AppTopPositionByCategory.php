<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppTopPositionByCategory extends Model
{
    protected $table = 'app_top_position_by_category';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'category_id',
        'position',
    ];
}
