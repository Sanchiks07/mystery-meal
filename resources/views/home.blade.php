<!DOCTYPE html>
<html>
<head>
    <title>Fridge Recipe Finder</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

@php

$categories = [

    'Meat' => [
        'Chicken',
        'Beef',
        'Bacon',
        'Ham',
        'Sausage',
        'Turkey'
    ],

    'Fish' => [
        'Salmon',
        'Tuna',
        'Shrimp'
    ],

    'Dairy' => [
        'Cheese',
        'Milk',
        'Butter',
        'Mozzarella',
        'Parmesan',
        'Cream',
        'Yogurt'
    ],

    'Vegetables' => [
        'Tomato',
        'Potato',
        'Onion',
        'Garlic',
        'Carrot',
        'Mushroom',
        'Corn',
        'Cucumber',
        'Broccoli',
        'Spinach',
        'Cabbage',
        'Peas',
        'Lettuce'
    ],

    'Fruits' => [
        'Apple',
        'Banana',
        'Orange',
        'Lemon',
        'Strawberry',
        'Blueberry',
        'Avocado'
    ],

    'Grains' => [
        'Rice',
        'Pasta',
        'Bread',
        'Flour',
        'Rice Noodles',
        'Oats'
    ],

    'Other' => [
        'Egg',
        'Sugar',
        'Salt',
        'Pepper',
        'Chocolate',
        'Honey'
    ]

];

@endphp

<div class="layout">

    <aside class="sidebar">

        <div class="sidebar-top">

            <h2>Ingredients</h2>

            <button
                form="recipeForm"
                class="search-btn"
            >
                Find Recipes
            </button>

        </div>

        <form
            method="POST"
            action="/search"
            id="recipeForm"
        >

            @csrf

            <div class="ingredient-scroll">

                @foreach($categories as $category => $items)

                    <div class="category">

                        <h3>
                            {{ $category }}
                        </h3>

                        <div class="ingredient-list">

                            @foreach($items as $ingredient)

                                <label class="ingredient-item">

                                    <input
                                        type="checkbox"
                                        name="ingredients[]"
                                        value="{{ $ingredient }}"

                                        @if(
                                            isset($selected)
                                            &&
                                            in_array($ingredient, $selected)
                                        )
                                            checked
                                        @endif
                                    >

                                    <span>
                                        {{ $ingredient }}
                                    </span>

                                </label>

                            @endforeach

                        </div>

                    </div>

                @endforeach

            </div>

        </form>

    </aside>

    <main class="content">

        <div class="hero">

            <h1>Fridge Recipe Finder</h1>

            <p>
                Find recipes using ingredients from your fridge
            </p>

        </div>

        @if(isset($selected) && count($selected) > 0)

            <div class="selected-box">

                @foreach($selected as $item)

                    <form
                        method="POST"
                        action="/search"
                        class="tag-form"
                    >

                        @csrf

                        @foreach($selected as $keep)

                            @if($keep != $item)

                                <input
                                    type="hidden"
                                    name="ingredients[]"
                                    value="{{ $keep }}"
                                >

                            @endif

                        @endforeach

                        <button class="selected-tag">

                            {{ $item }}

                            <span>
                                ×
                            </span>

                        </button>

                    </form>

                @endforeach

            </div>

        @endif

        @if(isset($recipes))

            @if(count($recipes) > 0)

                <div class="recipes-grid">

                    @foreach($recipes as $recipe)

                        <div class="recipe-card">

                            <img
                                src="{{ $recipe['image'] }}"
                                alt="{{ $recipe['name'] }}"
                            >

                            <div class="recipe-content">

                               <h2>
                                    <a href="/recipe/{{ $recipe['id'] }}" class="recipe-title">
                                        {{ $recipe['name'] }}
                                    </a>
                                </h2>
                                <div class="matched-products">

                                    @foreach($recipe['matchedIngredients'] as $ingredient)

                                        <span class="matched-tag">

                                            {{ ucfirst($ingredient) }}

                                        </span>

                                    @endforeach

                                </div>
                                <div class="time-box">
                                    ⏱ {{ $recipe['prepTimeMinutes'] ?? 'N/A' }} min
                                </div>
                                <h3>
                                    Missing Ingredients
                                </h3>

                                <ul>

                                    @foreach($recipe['missing'] as $item)

                                        <li>
                                            {{ ucfirst($item) }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="empty">

                    No recipes found.

                </div>

            @endif

        @endif

    </main>

</div>

</body>
</html>