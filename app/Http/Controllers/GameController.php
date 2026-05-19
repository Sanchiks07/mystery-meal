<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Highscore;

class GameController extends Controller
{
    public function index()
    {
        $highscore = Highscore::where('user_id', Auth::id())->first();

        return view('game', [
            'bestScore' => $highscore ? $highscore->score : 0
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|min:0'
        ]);

        $highscore = Highscore::where('user_id', Auth::id())->first();

        if (!$highscore) {

            Highscore::create([
                'user_id' => Auth::id(),
                'score' => $request->score
            ]);

        } else if ($request->score > $highscore->score) {

            $highscore->update([
                'score' => $request->score
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function highscores()
    {
        $scores = Highscore::with('user')
            ->orderBy('score', 'desc')
            ->take(10)
            ->get();

        return view('highscores', compact('scores'));
    }
}