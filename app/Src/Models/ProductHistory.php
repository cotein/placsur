<?php

namespace App\Src\Models;

use Illuminate\Database\Eloquent\Model;

class ProductHistory extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'data' => 'array'
    ];
}
