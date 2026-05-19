<x-layout>

<div class="content">

    <div class="hero">
        <h1>My Recipes</h1>
        <p class="hero-subtitle">Recipes you've created</p>
    </div>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(count($recipes) > 0)

        <div class="recipes-grid">

            @foreach($recipes as $recipe)

                <div class="recipe-card">

                    <img src="{{ $recipe['image'] }}" alt="{{ $recipe['name'] }}">

                    <div class="recipe-content">

                        <h2>
                            <a href="/recipe/{{ $recipe['id'] }}" class="recipe-title">{{ $recipe['name'] }}</a>
                        </h2>

                        <div class="time-box">
                            ⏱ {{ $recipe['prepTimeMinutes'] }} min
                        </div>

                        <div style="margin-top:16px;">
                            <a href="/recipes/{{ $recipe['user_recipe_id'] }}/edit" class="edit-btn">Edit</a>
                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty">
            You haven't created any recipes yet. <a href="{{ route('recipes.create') }}" style="color:#ffcc00;text-decoration:none;font-weight:bold;">Create one now</a>
        </div>

    @endif

</div>

</x-layout>
