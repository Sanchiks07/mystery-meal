<x-layout>
<div class="highscore-container">
    <h1>Leaderboard</h1>

    @if($scores->isEmpty())
        <p>No scores yet. Go play instead of staring at this.</p>
    @else
        <ul>
            @foreach($scores as $score)
                <li>{{ $score->user->name }} - {{ $score->score }}</li>
            @endforeach
        </ul>
    @endif
</div>
</x-layout>