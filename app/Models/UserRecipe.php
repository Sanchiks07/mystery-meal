<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRecipe extends Model
{
    protected $fillable = [
        'title',
        'ingredients',
        'cook_time',
        'instructions'
    ];
}