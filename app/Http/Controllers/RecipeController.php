<?php

namespace App\Http\Controllers;

use App\Models\UserRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Favorite;

class RecipeController extends Controller
{
    public function index()
{
    // Show user recipes on home page
    $userRecipes = UserRecipe::all();
    $recipes = [];

    foreach ($userRecipes as $userRecipe) {
        $recipes[] = [
            'id' => 'user-' . $userRecipe->id,
            'name' => $userRecipe->title,
            'image' => asset('storage/' . $userRecipe->image),
            'prepTimeMinutes' => $userRecipe->cook_time,
            'instructions' => $userRecipe->instructions,
            'ingredients' => json_decode($userRecipe->ingredients, true) ?? [],
            'matchedIngredients' => [],
            'missing' => [],
            'is_user_recipe' => true,
            'user_recipe_id' => $userRecipe->id
        ];
    }

    return view('home', ['recipes' => $recipes]);
}       // Show user recipes on home page\n        $userRecipes = UserRecipe::all();\n        $recipes = [];\n        \n        foreach ($userRecipes as $userRecipe) {\n            $recipes[] = [\n                'id' => 'user-' . $userRecipe->id,\n                'name' => $userRecipe->title,\n                'image' => asset('storage/' . $userRecipe->image),\n                'prepTimeMinutes' => $userRecipe->cook_time,\n                'instructions' => $userRecipe->instructions,\n                'ingredients' => json_decode($userRecipe->ingredients, true) ?? [],\n                'matchedIngredients' => [],\n                'missing' => [],\n                'is_user_recipe' => true,\n                'user_recipe_id' => $userRecipe->id\n            ];\n        }\n\n        return view('home', ['recipes' => $recipes]);\n    }

    public function search(Request $request)
    {
        $selectedIngredients = $request->ingredients ?? [];

        $response = Http::get('https://dummyjson.com/recipes');

        $recipes = $response->json()['recipes'];

        $matchedRecipes = [];

        foreach ($recipes as $recipe) {

            $recipeIngredients = array_map('strtolower', $recipe['ingredients']);
            $selectedLower = array_map('strtolower', $selectedIngredients);

            $matchedIngredients = [];

            foreach ($selectedLower as $selected) {
                foreach ($recipeIngredients as $ingredient) {
                    if (str_contains($ingredient, $selected)) {
                        $matchedIngredients[] = $selected;
                    }
                }
            }

            $matchedIngredients = array_unique($matchedIngredients);

            if (count($matchedIngredients) >= 1) {

                $missing = array_diff($recipeIngredients, $matchedIngredients);

                $recipe['matchedIngredients'] = $matchedIngredients;
                $recipe['missing'] = array_slice($missing, 0, 5);
                $recipe['is_user_recipe'] = false;

                $matchedRecipes[] = $recipe;
            }
        }

        // Add user recipes
        $userRecipes = UserRecipe::all();
        foreach ($userRecipes as $userRecipe) {
            $recipeIngredients = array_map('strtolower', json_decode($userRecipe->ingredients, true) ?? []);
            $selectedLower = array_map('strtolower', $selectedIngredients);

            $matchedIngredients = [];

            foreach ($selectedLower as $selected) {
                foreach ($recipeIngredients as $ingredient) {
                    if (str_contains($ingredient, $selected)) {
                        $matchedIngredients[] = $selected;
                    }
                }
            }

            $matchedIngredients = array_unique($matchedIngredients);

            if (count($matchedIngredients) >= 1) {
                $missing = array_diff($recipeIngredients, $matchedIngredients);

                $matchedRecipes[] = [
                    'id' => 'user-' . $userRecipe->id,
                    'name' => $userRecipe->title,
                    'image' => asset('storage/' . $userRecipe->image),
                    'prepTimeMinutes' => $userRecipe->cook_time,
                    'instructions' => $userRecipe->instructions,
                    'ingredients' => json_decode($userRecipe->ingredients, true) ?? [],
                    'matchedIngredients' => $matchedIngredients,
                    'missing' => array_slice($missing, 0, 5),
                    'is_user_recipe' => true,
                    'user_recipe_id' => $userRecipe->id
                ];
            }
        }

        usort($matchedRecipes, function($a, $b) {
            return count($b['matchedIngredients']) <=> count($a['matchedIngredients']);
        });

        return view('home', [
            'recipes' => $matchedRecipes,
            'selected' => $selectedIngredients
        ]);
    }

    // NEW PAGE (FULL RECIPE)
    public function show($id)
{
    // USER RECIPE
    if (str_starts_with($id, 'user-')) {

        $userId = str_replace('user-', '', $id);

        $userRecipe = UserRecipe::find($userId);

        if (!$userRecipe) {
            abort(404);
        }

        $recipe = [
            'id' => 'user-' . $userRecipe->id,
            'name' => $userRecipe->title,
            'image' => asset('storage/' . $userRecipe->image),
            'prepTimeMinutes' => $userRecipe->cook_time,
            'instructions' => $userRecipe->instructions,
            'ingredients' => json_decode($userRecipe->ingredients, true) ?? [],
            'is_user_recipe' => true
        ];

        return view('recipe', compact('recipe'));
    }

    // API RECIPE
    $response = Http::get("https://dummyjson.com/recipes");

    $recipes = $response->json()['recipes'];

    $recipe = collect($recipes)->firstWhere('id', (int)$id);

    if (!$recipe) {
        abort(404);
    }

    return view('recipe', compact('recipe'));
}
public function favorite(Request $request)
{
    $userId = auth()->id();
    $recipeId = $request->recipe_id;

    $exists = Favorite::where('user_id', $userId)
        ->where('recipe_id', $recipeId)
        ->exists();

    if ($exists) {
        return redirect()->route('home')->with('success', 'This recipe is already in your favorites.');
    }

    Favorite::create([
        'user_id' => $userId,
        'recipe_id' => $recipeId,
        'title' => $request->title,
        'image' => $request->image,
        'cook_time' => $request->cook_time
    ]);

    return redirect()->route('home')->with('success', 'Added to favorites!');
}
public function favorites()
{
    $recipes = Favorite::where('user_id', auth()->id())->get();

    return view('favorites', compact('recipes'));
}

public function unfavorite($id)
{
    $fav = Favorite::where('id', $id)->where('user_id', auth()->id())->first();
    if ($fav) {
        $fav->delete();
    }

    return redirect()->route('favorites')->with('success', 'Removed from favorites');
}
}