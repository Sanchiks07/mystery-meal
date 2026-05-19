<x-layout>

@php

$categories = [

    'Meat' => [
        'Chicken',
        'Beef',
        'Bacon',
        'Ham'
    ],

    'Fish' => [
        'Salmon',
        'Tuna',
        'Shrimp'
    ],

    'Dairy' => [
        'Cheese',
        'Mozzarella',
        'Parmesan',
        'Milk',
        'Butter',
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
        'Bell Pepper',
        'Avocado'
    ],

    'Fruits' => [
        'Apple',
        'Banana',
        'Lemon',
        'Strawberry'
    ],

    'Grains' => [
        'Rice',
        'Pasta',
        'Bread',
        'Flour',
        'Noodles'
    ],

    'Spices & Sauces' => [
        'Salt',
        'Pepper',
        'Olive Oil',
        'Soy Sauce',
        'Tomato Sauce',
        'Honey',
        'Sugar',
        'Cinnamon'
    ],

    'Other' => [
        'Egg',
        'Chocolate',
        'Basil',
        'Chicken Broth',
        'Vanilla'
    ]

];

@endphp

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <form
            method="POST"
            action="/search"
            id="recipeForm"
        >

            @csrf

            <div class="sidebar-top">

                <h2>Ingredients</h2>

                <button
                    type="submit"
                    class="search-btn"
                >
                    Find Recipes
                </button>

            </div>

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

    <!-- MAIN CONTENT -->
    <main class="content">

        @if(!isset($recipes))

            <div class="hero">

                <div class="hero-content">
                    <h1>Recipe Discovery</h1>

                    <p class="hero-subtitle">
                        Select ingredients you have at home and discover recipes you can make.
                    </p>

                    <p class="hero-guide">
                        Select ingredients from the sidebar and click "Find Recipes" to get started.
                    </p>
                </div>

            </div>

        @endif

        @if(session('success'))

            <div class="success-message">
                {{ session('success') }}
            </div>

        @endif

        <div id="search-state-container">

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

                            <button
                                type="submit"
                                class="selected-tag"
                            >

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

                                <!-- IMAGE -->
                                <img
                                    src="{{ $recipe['image'] }}"
                                    alt="{{ $recipe['name'] }}"
                                >

                                <!-- FAVORITE BUTTON -->
                                <form
                                    method="POST"
                                    action="/favorite"
                                    class="favorite-form"
                                >

                                    @csrf

                                    <input
                                        type="hidden"
                                        name="recipe_id"
                                        value="{{ $recipe['id'] }}"
                                    >

                                    <input
                                        type="hidden"
                                        name="title"
                                        value="{{ $recipe['name'] }}"
                                    >

                                    <input
                                        type="hidden"
                                        name="image"
                                        value="{{ $recipe['image'] }}"
                                    >

                                    <input
                                        type="hidden"
                                        name="cook_time"
                                        value="{{ $recipe['prepTimeMinutes'] ?? 0 }}"
                                    >

                                    <button
                                        type="submit"
                                        class="favorite-btn"
                                    >
                                        ❤
                                    </button>

                                </form>

                                <!-- CONTENT -->
                                <div class="recipe-content">

                                    <h2>

                                        <a
                                            href="/recipe/{{ $recipe['id'] }}"
                                            class="recipe-title"
                                        >
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

                @elseif(isset($selected) && count($selected) > 0)

                    <div class="empty">

                        No recipes found.

                    </div>

                @endif

            @endif

        </div>

    </main>

</div>

</x-layout>