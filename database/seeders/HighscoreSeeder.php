<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Highscore;

class HighscoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Highscore::create([
            'user_id' => 1,
            'score' => 1523
        ]);

        Highscore::create([
            'user_id' => 2,
            'score' => 1421
        ]);

        Highscore::create([
            'user_id' => 3,
            'score' => 1558
        ]);

        Highscore::create([
            'user_id' => 4,
            'score' => 1437
        ]);

        Highscore::create([
            'user_id' => 5,
            'score' => 1557
        ]);

        Highscore::create([
            'user_id' => 6,
            'score' => 1530
        ]);

        Highscore::create([
            'user_id' => 7,
            'score' => 1447
        ]);
    }
}
