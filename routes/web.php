<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\GameController;

use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserRecipeController;

Route::get('/', [RecipeController::class, 'index'])->name('home')->middleware('auth');

Route::post('/search', [RecipeController::class, 'search']);

Route::get('/recipe/{id}', [RecipeController::class, 'show']);

// USER RECIPES
Route::get('/recipes/create', [UserRecipeController::class, 'create'])->name('recipes.create')->middleware('auth');
Route::post('/recipes', [UserRecipeController::class, 'store'])->name('recipes.store')->middleware('auth');

// LOGIN
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'store']);

// REGISTER
Route::get('/register', [RegisterController::class, 'index'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'store']);

// LOGOUT
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// GAME
Route::get('/game', [GameController::class, 'index'])->name('game')->middleware('auth');
Route::post('/save-score', [GameController::class, 'save'])->name('save-score')->middleware('auth');
Route::get('/highscores', [GameController::class, 'highscores'])->name('highscores')->middleware('auth');

// favorites 
Route::post('/favorite', [RecipeController::class, 'favorite'])
    ->middleware('auth')
    ->name('favorite');

Route::post('/favorite/{id}/remove', [RecipeController::class, 'unfavorite'])
    ->middleware('auth')
    ->name('favorite.remove');

Route::get('/favorites', [RecipeController::class, 'favorites'])
    ->middleware('auth')
    ->name('favorites');

