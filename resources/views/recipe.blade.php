<x-layout>
    <title>{{ $recipe['name'] ?? 'Recipe' }}</title>

    <link rel="stylesheet" href="{{ asset('css/recipe.css') }}">
</head>
<body>

<div class="recipe-page">

    <a href="/" class="back-btn">← Back</a>

    <h1>{{ $recipe['name'] ?? $recipe['title'] }}</h1>

    <img src="{{ $recipe['image'] }}" class="recipe-big-img">

    <div class="info-row">

        <div class="info-box">
            ⏱ {{ $recipe['prepTimeMinutes'] ?? 'N/A' }} min
        </div>

    </div>

    <div class="section">

        <h2>Ingredients</h2>

        <ul>
            @foreach($recipe['ingredients'] as $ing)
                <li>{{ $ing }}</li>
            @endforeach
        </ul>

    </div>

    <div class="section">

        <h2>Instructions</h2>

        <div class="instructions">

            @if(is_array($recipe['instructions']))

                <ol>
                    @foreach($recipe['instructions'] as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ol>

            @else

                <ol>
                    @foreach(explode('.', $recipe['instructions']) as $step)
                        @if(trim($step) != '')
                            <li>{{ trim($step) }}</li>
                        @endif
                    @endforeach
                </ol>

            @endif

        </div>
    </div>

</div>

</x-layout>