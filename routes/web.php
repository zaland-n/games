<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('games', [App\Http\Controllers\GameController::class, 'index']);
Route::get('games/create', [App\Http\Controllers\GameController::class, 'create']);
Route::post('games/store', [App\Http\Controllers\GameController::class, 'store']);
// Show single game
Route::get('games/show/{id}', [App\Http\Controllers\GameController::class, 'show']);
Route::get('games/edit/{id}', [App\Http\Controllers\GameController::class, 'edit']);
// Accept GET on the update URL and redirect to the edit view to avoid 405s
Route::get('games/update/{id}', [App\Http\Controllers\GameController::class, 'edit']);
Route::post('games/update/{id}', [App\Http\Controllers\GameController::class, 'update']);
// Delete (destroy) route - uses POST with CSRF-protected form
Route::post('games/destroy/{id}', [App\Http\Controllers\GameController::class, 'destroy']);
