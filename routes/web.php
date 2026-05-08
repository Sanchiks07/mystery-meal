<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;

Route::get('/', [RecipeController::class, 'index']);

Route::post('/search', [RecipeController::class, 'search']);

Route::get('/recipe/{id}', [RecipeController::class, 'show']);