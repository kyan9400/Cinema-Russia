<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\FavoritesController;

// order matters – specific routes first:
Route::get('/movies/popular', [MovieController::class, 'popular']);
Route::get('/movies/search',  [MovieController::class, 'search']);
Route::get('/movies/list', [MovieController::class, 'list']);
Route::get('/movies/{id}',    [MovieController::class, 'show']);

// Favorites routes
Route::get('/favorites', [FavoritesController::class, 'index']);
Route::post('/favorites/{movieId}', [FavoritesController::class, 'store']);
Route::delete('/favorites/{movieId}', [FavoritesController::class, 'destroy']);
Route::get('/favorites/check/{movieId}', [FavoritesController::class, 'check']);
