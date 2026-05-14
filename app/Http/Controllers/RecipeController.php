<?php

namespace App\Http\Controllers;

use App\Models\UserRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecipeController extends Controller
{
    public function index()
    {\n        // Show user recipes on home page\n        $userRecipes = UserRecipe::all();\n        $recipes = [];\n        \n        foreach ($userRecipes as $userRecipe) {\n            $recipes[] = [\n                'id' => 'user-' . $userRecipe->id,\n                'name' => $userRecipe->title,\n                'image' => asset('storage/' . $userRecipe->image),\n                'prepTimeMinutes' => $userRecipe->cook_time,\n                'instructions' => $userRecipe->instructions,\n                'ingredients' => json_decode($userRecipe->ingredients, true) ?? [],\n                'matchedIngredients' => [],\n                'missing' => [],\n                'is_user_recipe' => true,\n                'user_recipe_id' => $userRecipe->id\n            ];\n        }\n\n        return view('home', ['recipes' => $recipes]);\n    }

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
        $response = Http::get("https://dummyjson.com/recipes");

        $recipes = $response->json()['recipes'];

        $recipe = collect($recipes)->firstWhere('id', (int)$id);

        if (!$recipe) {
            abort(404);
        }

        return view('recipe', compact('recipe'));
    }
}