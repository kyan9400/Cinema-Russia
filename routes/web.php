<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));  // Main page shows popular movies
Route::get('/movies', fn() => view('movies'));          // your list page
Route::get('/favorites', fn() => view('favorites'));    // favorites page
Route::get('/movie/{id}', fn($id) => view('movie', ['id' => $id]));  // NEW details page
