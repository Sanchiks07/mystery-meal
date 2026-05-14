<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'recipe_id',
        'title',
        'image',
        'cook_time'
    ];
}