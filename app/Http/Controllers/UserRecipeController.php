<?php

namespace App\Http\Controllers;

use App\Models\UserRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserRecipeController extends Controller
{
    public function create()
    {
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

        return view('recipes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'string',
            'custom_ingredients' => 'nullable|string',
            'cook_time' => 'required|integer|min:1',
            'instructions' => 'required|string|min:10'
        ]);

        $ingredients = array_filter($request->ingredients);
        
        if ($request->custom_ingredients) {
            $customIngredients = array_map('trim', explode(',', $request->custom_ingredients));
            $ingredients = array_merge($ingredients, array_filter($customIngredients));
        }

        $imagePath = $request->file('image')->store('recipes', 'public');

        UserRecipe::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'image' => $imagePath,
            'ingredients' => json_encode($ingredients),
            'cook_time' => $validated['cook_time'],
            'instructions' => $validated['instructions']
        ]);

        return redirect('/')->with('success', 'Recipe created successfully!');
    }
}
