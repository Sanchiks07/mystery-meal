<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Score;
use Illuminate\Support\Facades\Auth;
use App\Models\Highscore;

class GameController extends Controller
{
    public function index() {
        return view('game');
    }

    public function save(Request $request) {
        $request->validate([
            'score' => 'required|integer|min:0'
        ]);

        Score::create([
            'user_id' => Auth::id(),
            'score' => $request->score
        ]);

        return response()->json(['success' => true]);
    }

    public function highscores() {
        $scores = Highscore::with('user')
            ->orderBy('score', 'desc')
            ->take(10)
            ->get();

        return view('highscores', compact('scores'));
    }
}