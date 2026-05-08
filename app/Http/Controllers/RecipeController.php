<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecipeController extends Controller
{
    public function index()
    {
        return view('home');
    }

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

                $matchedRecipes[] = $recipe;
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