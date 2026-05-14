<x-layout>
    <div class="game-container">
        <h1>Food Mini-Game</h1>

        <div class="stats">
            <p>Score: <span id="score">0</span></p>
            <p>Best: <span id="bestScore">{{ $bestScore }}</span></p>
        </div>

        <canvas id="gameCanvas" width="500"  height="600"> </canvas>

        <p id="gameOver"></p>
    </div>

    <script>
        window.savedBestScore = {{ $bestScore ?? 0 }};
    </script>
</x-layout>