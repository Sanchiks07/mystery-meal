<x-layout>
<div class="highscore-container">
    <h1>Leaderboard</h1>

    @if($scores->isEmpty())
        <p>No scores yet. Go play instead of staring at this.</p>
    @else
        <table class="scores-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scores as $index => $score)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $score->user->name }}</td>
                        <td>{{ $score->score }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
</x-layout>