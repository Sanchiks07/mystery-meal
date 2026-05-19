@auth
<nav class="navigation">
    <!-- LEFT SIDE -->
    <div class="nav-left">
        <a href="{{ route('home') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
        <a href="{{ route('game') }}" class="{{ request()->is('game') ? 'active' : '' }}">Game</a>
        <a href="{{ route('highscores') }}" class="{{ request()->is('highscores') ? 'active' : '' }}">Highscore</a>
        <a href="{{ route('recipes.create') }}" class="{{ request()->is('recipes/create') ? 'active' : '' }}">Create Recipe</a>
    </div>

    <!-- HAMBURGER -->
    <button class="nav-toggle" onclick="toggleNav()">☰</button>

    <!-- RIGHT SIDE -->
    <div class="nav-right" id="navMenu">
        <span>Welcome, {{ auth()->user()->name }}!</span>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</nav>

<!-- HAMBURGER SCRIPT -->
<script>
    function toggleNav() {
        document.getElementById('navMenu').classList.toggle('active');
    }
</script>
@endauth