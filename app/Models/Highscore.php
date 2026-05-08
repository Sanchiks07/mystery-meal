<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Highscore extends Model
{
    protected $fillable = [
        'user_id',
        'score'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
