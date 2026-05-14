<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRecipe extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'image',
        'ingredients',
        'cook_time',
        'instructions'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}