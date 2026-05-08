<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HighscoreController extends Controller
{
    public function index() {
        return view('highscore');
    }
}