<x-layout>

<div class="content">

    <h1>Your Favorites</h1>

    <div class="recipes-grid">

        @foreach($recipes as $recipe)

            <div class="recipe-card">

                <img src="{{ $recipe->image }}">

                <div class="recipe-content">

                    <h2>
                        <a href="/recipe/{{ $recipe->recipe_id }}" class="recipe-title">{{ $recipe->title }}</a>
                    </h2>

                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="time-box">
                            ⏱ {{ $recipe->cook_time }} min
                        </div>

                        <form method="POST" action="{{ route('favorite.remove', $recipe->id) }}">

                            @csrf

                            <button type="submit" class="logout-form" style="padding:8px 12px;border-radius:8px;background:transparent;border:1px solid rgba(255,255,255,0.08);color:#fff;cursor:pointer;">Unfavorite</button>

                        </form>
                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>

</x-layout>